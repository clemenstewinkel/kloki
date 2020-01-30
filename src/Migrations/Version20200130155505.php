<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200130155505 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bestuhlungsplan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sitzplaetze INT NOT NULL, stehplaetze INT NOT NULL, pdf_file_path VARCHAR(255) NOT NULL, png_file_path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stage_order (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, pdf_file_name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, png_file_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE klo_ki_event (id INT AUTO_INCREMENT NOT NULL, kategorie_id INT NOT NULL, kontakt_id INT NOT NULL, best_plan_id INT DEFAULT NULL, room_id INT NOT NULL, parent_event_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, stage_order_id INT DEFAULT NULL, helper_einlass_eins_id INT DEFAULT NULL, helper_einlass_zwei_id INT DEFAULT NULL, helper_kasse_id INT DEFAULT NULL, helper_springer_eins_id INT DEFAULT NULL, helper_springer_zwei_id INT DEFAULT NULL, helper_garderobe_id INT DEFAULT NULL, licht_techniker_id INT DEFAULT NULL, ton_techniker_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, art ENUM(\'rental\', \'show\', \'fair\') NOT NULL COMMENT \'(DC2Type:EventArtType)\', contract_state ENUM(\'none\', \'requested\', \'sent\', \'received\') NOT NULL COMMENT \'(DC2Type:ContractStateType)\', start DATETIME NOT NULL, end DATETIME DEFAULT NULL, anzahl_artists INT DEFAULT NULL, is_best_benoetigt TINYINT(1) NOT NULL, is_fixed TINYINT(1) NOT NULL, is_licht_benoetigt TINYINT(1) NOT NULL, bemerkung LONGTEXT DEFAULT NULL, is_ton_benoetigt TINYINT(1) NOT NULL, helper_required TINYINT(1) NOT NULL, all_day TINYINT(1) DEFAULT NULL, is4_hours_deal TINYINT(1) DEFAULT NULL, is_reduced_price TINYINT(1) NOT NULL, is4h_price TINYINT(1) NOT NULL, hotel_state ENUM(\'none\', \'needed\', \'booked\') NOT NULL COMMENT \'(DC2Type:HotelStateType)\', press_material_state ENUM(\'none\', \'needed\', \'available\') NOT NULL COMMENT \'(DC2Type:PressMaterialStateType)\', overnight_stays INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6627298FBAF991D3 (kategorie_id), INDEX IDX_6627298FC4062E7F (kontakt_id), INDEX IDX_6627298FD7FD9E4F (best_plan_id), INDEX IDX_6627298F54177093 (room_id), INDEX IDX_6627298FEE3A445A (parent_event_id), INDEX IDX_6627298FDE12AB56 (created_by), INDEX IDX_6627298F16FE72E1 (updated_by), INDEX IDX_6627298F8578A955 (stage_order_id), INDEX IDX_6627298FBFC90FA6 (helper_einlass_eins_id), INDEX IDX_6627298F5FF70680 (helper_einlass_zwei_id), INDEX IDX_6627298F9AB16D31 (helper_kasse_id), INDEX IDX_6627298F6DD849D6 (helper_springer_eins_id), INDEX IDX_6627298F8DE640F0 (helper_springer_zwei_id), INDEX IDX_6627298F843AA1FD (helper_garderobe_id), INDEX IDX_6627298FA54117DF (licht_techniker_id), INDEX IDX_6627298FF3DADE48 (ton_techniker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE klo_ki_event_ausstattung (klo_ki_event_id INT NOT NULL, ausstattung_id INT NOT NULL, INDEX IDX_7B998D7B6D64B36B (klo_ki_event_id), INDEX IDX_7B998D7B953DE268 (ausstattung_id), PRIMARY KEY(klo_ki_event_id, ausstattung_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE klo_ki_event_user (klo_ki_event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_309FFDE16D64B36B (klo_ki_event_id), INDEX IDX_309FFDE1A76ED395 (user_id), PRIMARY KEY(klo_ki_event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ausstattung (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, bruttopreis INT NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE addresse (id INT AUTO_INCREMENT NOT NULL, vorname VARCHAR(255) DEFAULT NULL, nachname VARCHAR(255) NOT NULL, strasse VARCHAR(255) DEFAULT NULL, plz VARCHAR(8) DEFAULT NULL, ort VARCHAR(255) DEFAULT NULL, telefon VARCHAR(64) NOT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(7) DEFAULT NULL, full_day_price INT DEFAULT NULL, half_day_price INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE klo_ki_event_kategorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FBAF991D3 FOREIGN KEY (kategorie_id) REFERENCES klo_ki_event_kategorie (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FC4062E7F FOREIGN KEY (kontakt_id) REFERENCES addresse (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FD7FD9E4F FOREIGN KEY (best_plan_id) REFERENCES bestuhlungsplan (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FEE3A445A FOREIGN KEY (parent_event_id) REFERENCES klo_ki_event (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F8578A955 FOREIGN KEY (stage_order_id) REFERENCES stage_order (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FBFC90FA6 FOREIGN KEY (helper_einlass_eins_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F5FF70680 FOREIGN KEY (helper_einlass_zwei_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F9AB16D31 FOREIGN KEY (helper_kasse_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F6DD849D6 FOREIGN KEY (helper_springer_eins_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F8DE640F0 FOREIGN KEY (helper_springer_zwei_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298F843AA1FD FOREIGN KEY (helper_garderobe_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FA54117DF FOREIGN KEY (licht_techniker_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event ADD CONSTRAINT FK_6627298FF3DADE48 FOREIGN KEY (ton_techniker_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE klo_ki_event_ausstattung ADD CONSTRAINT FK_7B998D7B6D64B36B FOREIGN KEY (klo_ki_event_id) REFERENCES klo_ki_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE klo_ki_event_ausstattung ADD CONSTRAINT FK_7B998D7B953DE268 FOREIGN KEY (ausstattung_id) REFERENCES ausstattung (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE klo_ki_event_user ADD CONSTRAINT FK_309FFDE16D64B36B FOREIGN KEY (klo_ki_event_id) REFERENCES klo_ki_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE klo_ki_event_user ADD CONSTRAINT FK_309FFDE1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresse (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FD7FD9E4F');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F8578A955');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FEE3A445A');
        $this->addSql('ALTER TABLE klo_ki_event_ausstattung DROP FOREIGN KEY FK_7B998D7B6D64B36B');
        $this->addSql('ALTER TABLE klo_ki_event_user DROP FOREIGN KEY FK_309FFDE16D64B36B');
        $this->addSql('ALTER TABLE klo_ki_event_ausstattung DROP FOREIGN KEY FK_7B998D7B953DE268');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FDE12AB56');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F16FE72E1');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FBFC90FA6');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F5FF70680');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F9AB16D31');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F6DD849D6');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F8DE640F0');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F843AA1FD');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FA54117DF');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FF3DADE48');
        $this->addSql('ALTER TABLE klo_ki_event_user DROP FOREIGN KEY FK_309FFDE1A76ED395');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FC4062E7F');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F5B7AF75');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298F54177093');
        $this->addSql('ALTER TABLE klo_ki_event DROP FOREIGN KEY FK_6627298FBAF991D3');
        $this->addSql('DROP TABLE bestuhlungsplan');
        $this->addSql('DROP TABLE stage_order');
        $this->addSql('DROP TABLE klo_ki_event');
        $this->addSql('DROP TABLE klo_ki_event_ausstattung');
        $this->addSql('DROP TABLE klo_ki_event_user');
        $this->addSql('DROP TABLE ausstattung');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE addresse');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE klo_ki_event_kategorie');
    }
}
