<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191210101630 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE klo_ki_event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, begin_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, anzahl_artists INTEGER DEFAULT NULL, is_best_benoetigt BOOLEAN NOT NULL, is_licht_benoetigt BOOLEAN NOT NULL, bemerkung CLOB DEFAULT NULL, is_fixed BOOLEAN NOT NULL, is_ton_benoetigt BOOLEAN NOT NULL, helper_required BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, parent_event_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE TABLE klo_ki_event_ausstattung (klo_ki_event_id INTEGER NOT NULL, ausstattung_id INTEGER NOT NULL, PRIMARY KEY(klo_ki_event_id, ausstattung_id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE klo_ki_event');
        $this->addSql('DROP TABLE klo_ki_event_ausstattung');
    }
}
