<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200216144641 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'User-Adresse-Relation jetzt ManyToOne: Ein User hat eine Adresse, aber eine Adresse kann zu vielen Usern gehören.';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE user DROP INDEX UNIQ_8D93D649F5B7AF75, ADD INDEX IDX_8D93D649F5B7AF75 (address_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE user DROP INDEX IDX_8D93D649F5B7AF75, ADD UNIQUE INDEX UNIQ_8D93D649F5B7AF75 (address_id)');
    }
}
