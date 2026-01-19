<?php

namespace App\Command;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:games:import-bgg-ranks',
    description: 'Importe le CSV bg_ranks (id,name,yearpublished,rank,average,usersrated,is_expansion,...) dans la table jeu.'
)]
final class ImportBggRanksCsvCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private GameRepository $repo
    ) { parent::__construct(); }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Chemin vers bg_ranks.csv')
            ->addOption('flush-every', null, InputOption::VALUE_REQUIRED, 'Flush toutes les N lignes', 2000)
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limiter le nombre de lignes (debug)', 0)
            ->addOption('skip-expansions', null, InputOption::VALUE_NONE, 'Ignore les lignes is_expansion=1');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln("<error>Fichier introuvable: $file</error>");
            return Command::FAILURE;
        }

        $flushEvery = max(1, (int) $input->getOption('flush-every'));
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
            $idx[strtolower(trim((string)$col))] = $i;
        }

        // Obligatoires
        foreach (['id', 'name'] as $col) {
            if (!isset($idx[$col])) {
                fclose($h);
                $output->writeln("<error>Colonne requise manquante: $col</error>");
                return Command::FAILURE;
            }
        }

        // Optionnelles connues du bg_ranks
        $iId = $idx['id'];
        $iName = $idx['name'];
        $iYear = $idx['yearpublished'] ?? null;
        $iRank = $idx['rank'] ?? null;
        $iAvg = $idx['average'] ?? null;
        $iUsersRated = $idx['usersrated'] ?? null;
        $iIsExpansion = $idx['is_expansion'] ?? null;

        $n = 0; $created = 0; $updated = 0; $skipped = 0;

        while (($row = fgetcsv($h)) !== false) {
            $n++;
            if ($limit > 0 && $n > $limit) break;

            $bggId = trim((string)($row[$iId] ?? ''));
            $name  = trim((string)($row[$iName] ?? ''));

            if ($bggId === '' || $name === '') { $skipped++; continue; }

            $isExpansion = false;
            if ($iIsExpansion !== null) {
                $isExpansion = ((int)($row[$iIsExpansion] ?? 0)) === 1;
            }
            if ($skipExpansions && $isExpansion) { $skipped++; continue; }

            $game = $this->repo->findOneByBggId($bggId);
            if (!$game) { $game = new Game(); $created++; } else { $updated++; }

            $game->setBggId($bggId);
            $game->setName($name);

            if ($iYear !== null && method_exists($game, 'setYear')) {
                $yr = trim((string)($row[$iYear] ?? ''));
                $game->setYear($yr !== '' ? (int)$yr : null);
            }

            // Champs optionnels : seulement si tu les as ajoutés
            if (method_exists($game, 'setIsExpansion')) {
                $game->setIsExpansion($isExpansion);
            }

            if ($iRank !== null && method_exists($game, 'setBggRank')) {
                $r = trim((string)($row[$iRank] ?? ''));
                $game->setBggRank($r !== '' ? (int)$r : null);
            }

            if ($iAvg !== null && method_exists($game, 'setBggAverage')) {
                $a = trim((string)($row[$iAvg] ?? ''));
                $game->setBggAverage($a !== '' ? (float)$a : null);
            }

            if ($iUsersRated !== null && method_exists($game, 'setBggUsersRated')) {
                $u = trim((string)($row[$iUsersRated] ?? ''));
                $game->setBggUsersRated($u !== '' ? (int)$u : null);
            }

            $this->em->persist($game);

            if (($n % $flushEvery) === 0) {
                $this->em->flush();
                $this->em->clear();
                $output->writeln("... $n lignes traitées");
            }
        }

        fclose($h);

        $this->em->flush();
        $this->em->clear();

        $output->writeln("OK. Lignes=$n | Créés=$created | MAJ=$updated | Ignorés=$skipped");
        return Command::SUCCESS;
    }
}
