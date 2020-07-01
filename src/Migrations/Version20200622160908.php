<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200622160908 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Nettopreis statt Bruttopreis in Datenbank';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ausstattung CHANGE bruttopreis nettopreis INT NOT NULL');
        $this->addSql('UPDATE ausstattung SET nettopreis = nettopreis * 100 / 119');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE ausstattung CHANGE nettopreis bruttopreis INT NOT NULL');
        $this->addSql('UPDATE ausstattung SET bruttopreis = bruttopreis * 119 / 100');
    }
}
