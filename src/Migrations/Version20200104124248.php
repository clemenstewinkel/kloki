<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200104124248 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE ausstattung (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, bruttopreis INTEGER NOT NULL, description VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE bestuhlungsplan (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sitzplaetze INTEGER NOT NULL, stehplaetze INTEGER NOT NULL, pdf_file_path VARCHAR(255) NOT NULL, png_file_path VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE stage_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, pdf_file_name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, png_file_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE klo_ki_event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start DATETIME NOT NULL, "end" DATETIME DEFAULT NULL, anzahl_artists INTEGER DEFAULT NULL, is_best_benoetigt BOOLEAN NOT NULL, is_licht_benoetigt BOOLEAN NOT NULL, bemerkung CLOB DEFAULT NULL, is_fixed BOOLEAN NOT NULL, is_ton_benoetigt BOOLEAN NOT NULL, helper_required BOOLEAN NOT NULL, all_day BOOLEAN DEFAULT NULL, is4_hours_deal BOOLEAN DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, art_id INTEGER NOT NULL, kategorie_id INTEGER NOT NULL, kontakt_id INTEGER NOT NULL, best_plan_id INTEGER DEFAULT NULL, room_id INTEGER NOT NULL, parent_event_id INTEGER DEFAULT NULL, created_by INTEGER DEFAULT NULL, updated_by INTEGER DEFAULT NULL, stage_order_id INTEGER DEFAULT NULL, helper_einlass_eins_id INTEGER DEFAULT NULL, helper_einlass_zwei_id INTEGER DEFAULT NULL, helper_kasse_id INTEGER DEFAULT NULL, helper_springer_eins_id INTEGER DEFAULT NULL, helper_springer_zwei_id INTEGER DEFAULT NULL, licht_techniker_id INTEGER DEFAULT NULL, ton_techniker_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE TABLE klo_ki_event_ausstattung (klo_ki_event_id INTEGER NOT NULL, ausstattung_id INTEGER NOT NULL, PRIMARY KEY(klo_ki_event_id, ausstattung_id))');
        $this->addSql('CREATE TABLE klo_ki_event_user (klo_ki_event_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(klo_ki_event_id, user_id))');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, address_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F5B7AF75 ON user (address_id)');
        $this->addSql('CREATE TABLE klo_ki_event_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE addresse (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, vorname VARCHAR(255) DEFAULT NULL, nachname VARCHAR(255) NOT NULL, strasse VARCHAR(255) DEFAULT NULL, plz VARCHAR(8) DEFAULT NULL, ort VARCHAR(255) DEFAULT NULL, telefon VARCHAR(64) NOT NULL, email VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(7) DEFAULT NULL, full_day_price INTEGER DEFAULT NULL, half_day_price INTEGER DEFAULT NULL)');
        $this->addSql('CREATE TABLE klo_ki_event_kategorie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE ausstattung');
        $this->addSql('DROP TABLE bestuhlungsplan');
        $this->addSql('DROP TABLE stage_order');
        $this->addSql('DROP TABLE klo_ki_event');
        $this->addSql('DROP TABLE klo_ki_event_ausstattung');
        $this->addSql('DROP TABLE klo_ki_event_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE klo_ki_event_type');
        $this->addSql('DROP TABLE addresse');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE klo_ki_event_kategorie');
    }
}
