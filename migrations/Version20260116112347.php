<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260116112347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exemplaire_jeu (id BIGINT AUTO_INCREMENT NOT NULL, condition_state VARCHAR(30) DEFAULT NULL, location VARCHAR(120) DEFAULT NULL, purchase_date DATE DEFAULT NULL, notes LONGTEXT DEFAULT NULL, jeu_id BIGINT NOT NULL, proprietaire_id BIGINT NOT NULL, INDEX idx_copy_owner (proprietaire_id), INDEX idx_copy_game (jeu_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE groupe (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE hall_of_fame (total_points_global INT NOT NULL, seasons_count INT NOT NULL, titles_count INT DEFAULT NULL, rang_global INT DEFAULT NULL, groupe_id BIGINT NOT NULL, utilisateur_id BIGINT NOT NULL, INDEX IDX_9A98E3E17A45358C (groupe_id), INDEX IDX_9A98E3E1FB88E14F (utilisateur_id), INDEX idx_hof_rank (groupe_id, rang_global), UNIQUE INDEX uq_hof_group_user (groupe_id, utilisateur_id), PRIMARY KEY (groupe_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE jeu (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, publisher VARCHAR(180) DEFAULT NULL, year SMALLINT DEFAULT NULL, players_min SMALLINT DEFAULT NULL, players_max SMALLINT DEFAULT NULL, avg_duration_minutes SMALLINT DEFAULT NULL, bgg_id VARCHAR(50) DEFAULT NULL, UNIQUE INDEX uq_game_bgg (bgg_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `match` (id BIGINT AUTO_INCREMENT NOT NULL, date_match DATETIME NOT NULL, table_number SMALLINT DEFAULT NULL, round_number SMALLINT DEFAULT NULL, status VARCHAR(20) NOT NULL, tournoi_id BIGINT NOT NULL, INDEX IDX_7A5BC505F607770A (tournoi_id), INDEX idx_match_tournament_date (tournoi_id, date_match), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE membre_groupe (role VARCHAR(20) NOT NULL, joined_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL, groupe_id BIGINT NOT NULL, utilisateur_id BIGINT NOT NULL, INDEX IDX_9EB019987A45358C (groupe_id), INDEX idx_member_user (utilisateur_id), UNIQUE INDEX uq_group_member (groupe_id, utilisateur_id), PRIMARY KEY (groupe_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE participation_saison (registered_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL, saison_id BIGINT NOT NULL, utilisateur_id BIGINT NOT NULL, INDEX IDX_BAB553A3F965414C (saison_id), INDEX idx_participation_user (utilisateur_id), UNIQUE INDEX uq_season_participation (saison_id, utilisateur_id), PRIMARY KEY (saison_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE saison (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, statut VARCHAR(20) NOT NULL, points_rules JSON DEFAULT NULL, groupe_id BIGINT NOT NULL, INDEX IDX_C0D0D5867A45358C (groupe_id), INDEX idx_season_group_status (groupe_id, statut), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE score_match (score INT NOT NULL, position SMALLINT DEFAULT NULL, ranking_points INT NOT NULL, is_forfeit TINYINT NOT NULL, match_id BIGINT NOT NULL, utilisateur_id BIGINT NOT NULL, INDEX IDX_C317098D2ABEACD6 (match_id), INDEX idx_score_user (utilisateur_id), UNIQUE INDEX uq_match_user (match_id, utilisateur_id), PRIMARY KEY (match_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE stat_saison_joueur (total_points INT NOT NULL, total_wins INT NOT NULL, total_matches INT NOT NULL, rang_final INT DEFAULT NULL, saison_id BIGINT NOT NULL, utilisateur_id BIGINT NOT NULL, INDEX IDX_646478A6F965414C (saison_id), INDEX IDX_646478A6FB88E14F (utilisateur_id), INDEX idx_stat_rank (saison_id, rang_final), UNIQUE INDEX uq_stat_season_user (saison_id, utilisateur_id), PRIMARY KEY (saison_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE succes (id BIGINT AUTO_INCREMENT NOT NULL, code VARCHAR(60) NOT NULL, name VARCHAR(140) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(20) NOT NULL, `condition` JSON DEFAULT NULL, UNIQUE INDEX uq_achievement_code (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE succes_debloque (unlocked_at DATETIME NOT NULL, succes_id BIGINT NOT NULL, utilisateur_id BIGINT NOT NULL, saison_id BIGINT DEFAULT NULL, INDEX IDX_1EEAE87C4EF1B4AB (succes_id), INDEX IDX_1EEAE87CF965414C (saison_id), INDEX idx_unlock_user (utilisateur_id), UNIQUE INDEX uq_unlock_achievement_user_season (succes_id, utilisateur_id, saison_id), PRIMARY KEY (succes_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tournoi (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(140) NOT NULL, format VARCHAR(30) NOT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, status VARCHAR(20) NOT NULL, saison_id BIGINT NOT NULL, jeu_id BIGINT NOT NULL, INDEX idx_tournament_season (saison_id), INDEX idx_tournament_game (jeu_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE utilisateur (id BIGINT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, password_hash VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX uq_user_email (email), UNIQUE INDEX uq_user_pseudo (pseudo), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE exemplaire_jeu ADD CONSTRAINT FK_A501B6D28C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE exemplaire_jeu ADD CONSTRAINT FK_A501B6D276C50E4A FOREIGN KEY (proprietaire_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hall_of_fame ADD CONSTRAINT FK_9A98E3E17A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hall_of_fame ADD CONSTRAINT FK_9A98E3E1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `match` ADD CONSTRAINT FK_7A5BC505F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB019987A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB01998FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation_saison ADD CONSTRAINT FK_BAB553A3F965414C FOREIGN KEY (saison_id) REFERENCES saison (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation_saison ADD CONSTRAINT FK_BAB553A3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saison ADD CONSTRAINT FK_C0D0D5867A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE score_match ADD CONSTRAINT FK_C317098D2ABEACD6 FOREIGN KEY (match_id) REFERENCES `match` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE score_match ADD CONSTRAINT FK_C317098DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stat_saison_joueur ADD CONSTRAINT FK_646478A6F965414C FOREIGN KEY (saison_id) REFERENCES saison (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stat_saison_joueur ADD CONSTRAINT FK_646478A6FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE succes_debloque ADD CONSTRAINT FK_1EEAE87C4EF1B4AB FOREIGN KEY (succes_id) REFERENCES succes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE succes_debloque ADD CONSTRAINT FK_1EEAE87CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE succes_debloque ADD CONSTRAINT FK_1EEAE87CF965414C FOREIGN KEY (saison_id) REFERENCES saison (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DFF965414C FOREIGN KEY (saison_id) REFERENCES saison (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DF8C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exemplaire_jeu DROP FOREIGN KEY FK_A501B6D28C9E392E');
        $this->addSql('ALTER TABLE exemplaire_jeu DROP FOREIGN KEY FK_A501B6D276C50E4A');
        $this->addSql('ALTER TABLE hall_of_fame DROP FOREIGN KEY FK_9A98E3E17A45358C');
        $this->addSql('ALTER TABLE hall_of_fame DROP FOREIGN KEY FK_9A98E3E1FB88E14F');
        $this->addSql('ALTER TABLE `match` DROP FOREIGN KEY FK_7A5BC505F607770A');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB019987A45358C');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB01998FB88E14F');
        $this->addSql('ALTER TABLE participation_saison DROP FOREIGN KEY FK_BAB553A3F965414C');
        $this->addSql('ALTER TABLE participation_saison DROP FOREIGN KEY FK_BAB553A3FB88E14F');
        $this->addSql('ALTER TABLE saison DROP FOREIGN KEY FK_C0D0D5867A45358C');
        $this->addSql('ALTER TABLE score_match DROP FOREIGN KEY FK_C317098D2ABEACD6');
        $this->addSql('ALTER TABLE score_match DROP FOREIGN KEY FK_C317098DFB88E14F');
        $this->addSql('ALTER TABLE stat_saison_joueur DROP FOREIGN KEY FK_646478A6F965414C');
        $this->addSql('ALTER TABLE stat_saison_joueur DROP FOREIGN KEY FK_646478A6FB88E14F');
        $this->addSql('ALTER TABLE succes_debloque DROP FOREIGN KEY FK_1EEAE87C4EF1B4AB');
        $this->addSql('ALTER TABLE succes_debloque DROP FOREIGN KEY FK_1EEAE87CFB88E14F');
        $this->addSql('ALTER TABLE succes_debloque DROP FOREIGN KEY FK_1EEAE87CF965414C');
        $this->addSql('ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DFF965414C');
        $this->addSql('ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DF8C9E392E');
        $this->addSql('DROP TABLE exemplaire_jeu');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE hall_of_fame');
        $this->addSql('DROP TABLE jeu');
        $this->addSql('DROP TABLE `match`');
        $this->addSql('DROP TABLE membre_groupe');
        $this->addSql('DROP TABLE participation_saison');
        $this->addSql('DROP TABLE saison');
        $this->addSql('DROP TABLE score_match');
        $this->addSql('DROP TABLE stat_saison_joueur');
        $this->addSql('DROP TABLE succes');
        $this->addSql('DROP TABLE succes_debloque');
        $this->addSql('DROP TABLE tournoi');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
