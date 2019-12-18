<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191217191004 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE addresse ADD COLUMN email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__addresse AS SELECT id, vorname, nachname, strasse, plz, ort, telefon FROM addresse');
        $this->addSql('DROP TABLE addresse');
        $this->addSql('CREATE TABLE addresse (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, vorname VARCHAR(255) DEFAULT NULL, nachname VARCHAR(255) NOT NULL, strasse VARCHAR(255) DEFAULT NULL, plz VARCHAR(8) DEFAULT NULL, ort VARCHAR(255) DEFAULT NULL, telefon VARCHAR(64) NOT NULL)');
        $this->addSql('INSERT INTO addresse (id, vorname, nachname, strasse, plz, ort, telefon) SELECT id, vorname, nachname, strasse, plz, ort, telefon FROM __temp__addresse');
        $this->addSql('DROP TABLE __temp__addresse');
    }
}
