<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210314103909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F16FE72E1');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FDE12AB56');
        //$this->addSql('ALTER TABLE klo_ki_event CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FDE12AB56');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F16FE72E1');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
    }
}
