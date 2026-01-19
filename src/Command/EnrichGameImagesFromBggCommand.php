<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use App\Service\BggClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:games:enrich-images',
    description: 'Enrichit image_url et thumbnail_url des jeux via BGG (thing).'
)]
final class EnrichGameImagesFromBggCommand extends Command
{
    public function __construct(
        private Connection $db,
        private BggClient $bgg
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Nombre max de jeux à enrichir', 500)
            ->addOption('batch', null, InputOption::VALUE_REQUIRED, 'Nombre d’IDs par appel BGG', 20)
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Pause entre appels (secondes)', 5)
            ->addOption('extensions-only', null, InputOption::VALUE_NONE, 'Uniquement les extensions');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (int) $input->getOption('limit');
        $batch = (int) $input->getOption('batch');
        $sleep = (int) $input->getOption('sleep');
        $extensionsOnly = (bool) $input->getOption('extensions-only');

        // 1️⃣ Sélection des jeux sans image
        $sql = "
            SELECT bgg_id
            FROM jeu
            WHERE bgg_id IS NOT NULL
              AND image_url IS NULL
        ";

        if ($extensionsOnly) {
            $sql .= " AND is_expansion = 1";
        }

        $limit = max(1, (int) $limit);
        $sql .= " LIMIT $limit";

        $ids = $this->db->fetchFirstColumn($sql);


        if (!$ids) {
            $output->writeln('<info>Aucun jeu à enrichir.</info>');
            return Command::SUCCESS;
        }

        $output->writeln('Jeux à enrichir : ' . count($ids));

        // 2️⃣ Batch BGG
        foreach (array_chunk($ids, $batch) as $i => $chunk) {
            $output->writeln(sprintf(
                'Batch %d (%d jeux)',
                $i + 1,
                count($chunk)
            ));

            $data = $this->bgg->getThings($chunk); // méthode batch (voir plus bas)

            foreach ($data as $bggId => $img) {
                $this->db->executeStatement(
                    "UPDATE jeu
                     SET image_url = :image,
                         thumbnail_url = :thumb
                     WHERE bgg_id = :bggId",
                    [
                        'image' => $img['image'],
                        'thumb' => $img['thumbnail'],
                        'bggId' => $bggId,
                    ]
                );
            }

            if ($sleep > 0 && $i < count($ids) - 1) {
                sleep($sleep);
            }
        }

        $output->writeln('<info>Enrichissement des images terminé.</info>');
        return Command::SUCCESS;
    }
}
