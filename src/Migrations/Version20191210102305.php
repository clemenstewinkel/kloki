<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191210102305 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE klo_ki_event_user (klo_ki_event_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(klo_ki_event_id, user_id))');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN created_by INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN updated_by INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN helper_einlass_eins_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN helper_einlass_zwei_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN helper_kasse_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN helper_springer_eins_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN helper_springer_zwei_id INTEGER DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE klo_ki_event_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TEMPORARY TABLE __temp__klo_ki_event AS SELECT id, name, begin_at, end_at, anzahl_artists, is_best_benoetigt, is_licht_benoetigt, bemerkung, is_fixed, is_ton_benoetigt, helper_required, created_at, updated_at, parent_event_id FROM klo_ki_event');
        $this->addSql('DROP TABLE klo_ki_event');
        $this->addSql('CREATE TABLE klo_ki_event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, begin_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, anzahl_artists INTEGER DEFAULT NULL, is_best_benoetigt BOOLEAN NOT NULL, is_licht_benoetigt BOOLEAN NOT NULL, bemerkung CLOB DEFAULT NULL, is_fixed BOOLEAN NOT NULL, is_ton_benoetigt BOOLEAN NOT NULL, helper_required BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, parent_event_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO klo_ki_event (id, name, begin_at, end_at, anzahl_artists, is_best_benoetigt, is_licht_benoetigt, bemerkung, is_fixed, is_ton_benoetigt, helper_required, created_at, updated_at, parent_event_id) SELECT id, name, begin_at, end_at, anzahl_artists, is_best_benoetigt, is_licht_benoetigt, bemerkung, is_fixed, is_ton_benoetigt, helper_required, created_at, updated_at, parent_event_id FROM __temp__klo_ki_event');
        $this->addSql('DROP TABLE __temp__klo_ki_event');
    }
}
