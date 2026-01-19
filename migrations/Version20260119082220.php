<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119082220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jeu ADD nb_joueurs_min SMALLINT DEFAULT NULL, ADD nb_joueurs_max SMALLINT DEFAULT NULL, ADD duree_moyenne SMALLINT DEFAULT NULL, DROP players_min, DROP players_max, DROP avg_duration_minutes');
        $this->addSql('CREATE INDEX idx_game_name ON jeu (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_game_name ON jeu');
        $this->addSql('ALTER TABLE jeu ADD players_min SMALLINT DEFAULT NULL, ADD players_max SMALLINT DEFAULT NULL, ADD avg_duration_minutes SMALLINT DEFAULT NULL, DROP nb_joueurs_min, DROP nb_joueurs_max, DROP duree_moyenne');
    }
}
