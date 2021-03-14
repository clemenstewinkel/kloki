<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210314105222 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F5FF70680');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F6DD849D6');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F843AA1FD');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F8DE640F0');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F9AB16D31');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FA54117DF');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FBFC90FA6');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FF3DADE48');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F5FF70680 FOREIGN KEY (helper_einlass_zwei_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F6DD849D6 FOREIGN KEY (helper_springer_eins_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F843AA1FD FOREIGN KEY (helper_garderobe_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F8DE640F0 FOREIGN KEY (helper_springer_zwei_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F9AB16D31 FOREIGN KEY (helper_kasse_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FA54117DF FOREIGN KEY (licht_techniker_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FBFC90FA6 FOREIGN KEY (helper_einlass_eins_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FF3DADE48 FOREIGN KEY (ton_techniker_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FBFC90FA6');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F5FF70680');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F9AB16D31');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F6DD849D6');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F8DE640F0');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F843AA1FD');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FA54117DF');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FF3DADE48');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FBFC90FA6 FOREIGN KEY (helper_einlass_eins_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F5FF70680 FOREIGN KEY (helper_einlass_zwei_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F9AB16D31 FOREIGN KEY (helper_kasse_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F6DD849D6 FOREIGN KEY (helper_springer_eins_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F8DE640F0 FOREIGN KEY (helper_springer_zwei_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F843AA1FD FOREIGN KEY (helper_garderobe_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FA54117DF FOREIGN KEY (licht_techniker_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FF3DADE48 FOREIGN KEY (ton_techniker_id) REFERENCES user (id)');
    }
}
