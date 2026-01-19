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
    name: 'app:games:import-bgg',
    description: 'Importe des jeux depuis BGG XMLAPI2 (converti en JSON interne) vers la table jeu'
)]
final class ImportBggGamesCommand extends Command
{
    public function __construct(
        private BggClient $bgg,
        private GameRepository $games,
        private EntityManagerInterface $em
    ) { parent::__construct(); }

    protected function configure(): void
    {
        $this
            ->addArgument('query', InputArgument::REQUIRED, 'Recherche (ex: "Catan")')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Nombre de jeux à importer', 10)
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Pause entre appels thing (secondes)', 5);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = (string) $input->getArgument('query');
        $limit = (int) $input->getOption('limit');
        $sleep = (int) $input->getOption('sleep');

        $hits = $this->bgg->searchBoardgames($query, $limit);

        $created = 0; $updated = 0;

        foreach ($hits as $i => $hit) {
            $bggId = (int) $hit['id'];
            $data = $this->bgg->getThing($bggId);

            if (($data['name'] ?? '') === '') {
                continue;
            }

            // Upsert par bgg_id (string ou int selon ton entité)
            $entity = $this->games->findOneBy(['bggId' => (string) $bggId]);
            if (!$entity) {
                $entity = new Game();
                $created++;
            } else {
                $updated++;
            }

            $entity->setBggId((string) $bggId);
            $entity->setName($data['name']);
            $entity->setPublisher($data['publisher'] ?? null);
            $entity->setYear($data['year'] ?? null);
            $entity->setPlayersMin($data['players_min'] ?? null);
            $entity->setPlayersMax($data['players_max'] ?? null);
            $entity->setAvgDurationMinutes($data['duration_minutes'] ?? null);

            $this->em->persist($entity);
            $this->em->flush(); // flush petit à petit (ok au début)

            // Anti rate-limit (BGG throttle, ~5s recommandé)
            if ($i < count($hits) - 1 && $sleep > 0) {
                sleep($sleep);
            }
        }

        $output->writeln("OK. Créés: $created / MAJ: $updated");
        return Command::SUCCESS;
    }
}
