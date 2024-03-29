<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329093503 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Zwei unterschiedliche Preise für Räume und Ausstattung. Event ist jetzt "intern" oder nicht.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE room ADD full_day_price_intern INT DEFAULT NULL, ADD half_day_price_intern INT DEFAULT NULL' );
        $this->addSql('ALTER TABLE ausstattung ADD nettopreis_intern INT NOT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD is_intern_price TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE klo_ki_event DROP is_intern_price');
        $this->addSql('ALTER TABLE ausstattung DROP nettopreis_intern');
        $this->addSql('ALTER TABLE room DROP full_day_price_intern, DROP half_day_price_intern');
    }
}
