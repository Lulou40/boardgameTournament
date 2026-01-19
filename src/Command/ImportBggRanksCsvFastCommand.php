<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;



#[AsCommand(
    name: 'app:games:import-bgg-ranks-fast',
    description: 'Importe bg_ranks.csv en DBAL (faible RAM) avec UPSERT sur bgg_id.'
)]
final class ImportBggRanksCsvFastCommand extends Command
{
    public function __construct(private Connection $db)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Chemin vers bg_ranks.csv')
            ->addOption('batch', null, InputOption::VALUE_REQUIRED, 'Taille des lots', 2000)
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limiter le nombre de lignes (debug)', 0)
            ->addOption('skip-expansions', null, InputOption::VALUE_NONE, 'Ignore les lignes is_expansion=1')
            ->addOption('only-expansions', null, InputOption::VALUE_NONE, 'Importe uniquement les extensions');

    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln("<error>Fichier introuvable: $file</error>");
            return Command::FAILURE;
        }

        $batchSize = max(1, (int) $input->getOption('batch'));
        $limit = (int) $input->getOption('limit');
        $skipExpansions = (bool) $input->getOption('skip-expansions');

        $h = fopen($file, 'rb');
        if ($h === false) {
            $output->writeln("<error>Impossible d’ouvrir le fichier.</error>");
            return Command::FAILURE;
        }

        $header = fgetcsv($h);
        if (!$header) {
            fclose($h);
            $output->writeln("<error>CSV vide ou header illisible.</error>");
            return Command::FAILURE;
        }

        $idx = [];
        foreach ($header as $i => $col) {
            $idx[strtolower(trim((string) $col))] = $i;
        }

        foreach (['id', 'name'] as $col) {
            if (!isset($idx[$col])) {
                fclose($h);
                $output->writeln("<error>Colonne requise manquante: $col</error>");
                return Command::FAILURE;
            }
        }

        $iId = $idx['id'];
        $iName = $idx['name'];
        $iYear = $idx['yearpublished'] ?? null;
        $iIsExpansion = $idx['is_expansion'] ?? null;

        $platform = $this->db->getDatabasePlatform();
        $platformClass = get_class($platform);

        $isMySql = str_contains($platformClass, 'MySQL') || str_contains($platformClass, 'MariaDB');
        $isPostgres = str_contains($platformClass, 'PostgreSQL');

        $platformType = 'mysql';

        if ($isMySql) {
            $platformType = 'mysql';
        } elseif ($isPostgres) {
            $platformType = 'postgresql';
        } else {
            $platformType = 'sqlite'; // fallback
        }

        $output->writeln("Import en lots de $batchSize...");

        // Colonnes qu’on remplit depuis le CSV (compatibles avec ton entité actuelle)
        $cols = ['bgg_id', 'name', 'year', 'is_expansion'];

        $upsertSqlBuilder = function (int $rows) use ($platformType, $cols): string {
            $placeholdersPerRow = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
            $values = implode(',', array_fill(0, $rows, $placeholdersPerRow));

            $base = "INSERT INTO jeu (" . implode(',', $cols) . ") VALUES $values";

            if ($platformType === 'mysql') {
                return $base . " ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            year = VALUES(year),
            is_expansion = VALUES(is_expansion)";

            }

            if ($platformType === 'postgresql') {
                return $base . " ON CONFLICT (bgg_id) DO UPDATE SET
            name = EXCLUDED.name,
            year = EXCLUDED.year";
            }

            return $base . " ON CONFLICT(bgg_id) DO UPDATE SET
        name = excluded.name,
        year = excluded.year";
        };

        $batchParams = [];
        $rowsInBatch = 0;

        $read = 0;
        $writtenBatches = 0;
        $skipped = 0;

        $this->db->beginTransaction();
        try {
            while (($row = fgetcsv($h)) !== false) {
                $read++;
                if ($limit > 0 && $read > $limit)
                    break;

                $bggId = trim((string) ($row[$iId] ?? ''));
                $name = trim((string) ($row[$iName] ?? ''));

                if ($bggId === '' || $name === '') {
                    $skipped++;
                    continue;
                }

                $isExpansion = false;
                if ($iIsExpansion !== null) {
                    $isExpansion = ((int) ($row[$iIsExpansion] ?? 0)) === 1;
                }

                if ($input->getOption('only-expansions') && !$isExpansion) {
                    $skipped++;
                    continue;
                }

                if ($input->getOption('skip-expansions') && $isExpansion) {
                    $skipped++;
                    continue;
                }


                $year = null;
                if ($iYear !== null) {
                    $yr = trim((string) ($row[$iYear] ?? ''));
                    $year = ($yr !== '') ? (int) $yr : null;
                }

                // push params (bgg_id, name, year)
                $batchParams[] = $bggId;
                $batchParams[] = $name;
                $batchParams[] = $year;
                $batchParams[] = $isExpansion ? 1 : 0;


                $rowsInBatch++;

                if ($rowsInBatch >= $batchSize) {
                    $sql = $upsertSqlBuilder($rowsInBatch);
                    $this->db->executeStatement($sql, $batchParams);

                    $writtenBatches++;
                    $output->writeln("... batch #$writtenBatches (lignes lues: $read)");

                    $batchParams = [];
                    $rowsInBatch = 0;
                }
            }

            // flush restant
            if ($rowsInBatch > 0) {
                $sql = $upsertSqlBuilder($rowsInBatch);
                $this->db->executeStatement($sql, $batchParams);
                $writtenBatches++;
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            fclose($h);
            throw $e;
        }

        fclose($h);

        $output->writeln("Terminé. Lues=$read | Batches=$writtenBatches | Ignorées=$skipped");
        return Command::SUCCESS;
    }
}
