<?php

namespace App\Command;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Service\BggClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:games:import-bgg-file',
    description: 'Importe des jeux via BGG à partir d’un fichier (1 ligne = 1 jeu).'
)]
final class ImportBggGamesFromFileCommand extends Command
{
    public function __construct(
        private BggClient $bgg,
        private GameRepository $repo,
        private EntityManagerInterface $em
    ) { parent::__construct(); }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Chemin vers un .txt (1 jeu par ligne)')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Pause entre appels thing (secondes)', 5)
            ->addOption('search-limit', null, InputOption::VALUE_REQUIRED, 'Nb de résultats search à examiner', 5)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'N’écrit rien en base (debug)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $sleep = (int) $input->getOption('sleep');
        $searchLimit = (int) $input->getOption('search-limit');
        $dryRun = (bool) $input->getOption('dry-run');

        if (!is_file($file)) {
            $output->writeln("<error>Fichier introuvable: $file</error>");
            return Command::FAILURE;
        }

        $lines = array_values(array_filter(array_map('trim', file($file) ?: [])));
        if (!$lines) {
            $output->writeln("<error>Fichier vide.</error>");
            return Command::FAILURE;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($lines as $idx => $query) {
            $output->writeln("[$idx] Recherche: <info>$query</info>");

            $hits = $this->bgg->searchBoardgames($query, $searchLimit);
            if (!$hits) {
                $output->writeln("  -> <comment>Aucun résultat</comment>");
                $skipped++;
                continue;
            }

            // Choix simple et efficace : on prend le 1er résultat (le plus pertinent en général)
            $bggId = (int) $hits[0]['id'];

            $data = $this->bgg->getThing($bggId);
            if (($data['name'] ?? '') === '') {
                $output->writeln("  -> <comment>Détails vides (BGG)</comment>");
                $skipped++;
                continue;
            }

            $bggIdStr = (string) $bggId;
            $entity = $this->repo->findOneByBggId($bggIdStr);

            if (!$entity) {
                $entity = new Game();
                $created++;
            } else {
                $updated++;
            }

            $entity->setBggId($bggIdStr);
            $entity->setName($data['name']);
            $entity->setPublisher($data['publisher'] ?? null);
            $entity->setYear($data['year'] ?? null);
            $entity->setPlayersMin($data['players_min'] ?? null);
            $entity->setPlayersMax($data['players_max'] ?? null);
            $entity->setAvgDurationMinutes($data['duration_minutes'] ?? null);

            if ($dryRun) {
                $output->writeln("  -> (dry-run) {$data['name']} [BGG $bggIdStr]");
            } else {
                $this->em->persist($entity);
                $this->em->flush();
                $output->writeln("  -> OK: {$data['name']} [BGG $bggIdStr]");
            }

            // Anti-throttle
            if ($idx < count($lines) - 1 && $sleep > 0) {
                sleep($sleep);
            }
        }

        $output->writeln("Terminé. Créés=$created, MAJ=$updated, Ignorés=$skipped");
        return Command::SUCCESS;
    }
}
