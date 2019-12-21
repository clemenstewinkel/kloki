<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191220061723 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN licht_techniker_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE klo_ki_event ADD COLUMN ton_techniker_id INTEGER DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__klo_ki_event AS SELECT id, name, begin_at, end_at, anzahl_artists, is_best_benoetigt, is_licht_benoetigt, bemerkung, is_fixed, is_ton_benoetigt, helper_required, is_full_day, created_at, updated_at, art_id, kategorie_id, kontakt_id, best_plan_id, room_id, parent_event_id, created_by, updated_by, stage_order_id, helper_einlass_eins_id, helper_einlass_zwei_id, helper_kasse_id, helper_springer_eins_id, helper_springer_zwei_id FROM klo_ki_event');
        $this->addSql('DROP TABLE klo_ki_event');
        $this->addSql('CREATE TABLE klo_ki_event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, begin_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, anzahl_artists INTEGER DEFAULT NULL, is_best_benoetigt BOOLEAN NOT NULL, is_licht_benoetigt BOOLEAN NOT NULL, bemerkung CLOB DEFAULT NULL, is_fixed BOOLEAN NOT NULL, is_ton_benoetigt BOOLEAN NOT NULL, helper_required BOOLEAN NOT NULL, is_full_day BOOLEAN DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, art_id INTEGER NOT NULL, kategorie_id INTEGER NOT NULL, kontakt_id INTEGER NOT NULL, best_plan_id INTEGER DEFAULT NULL, room_id INTEGER NOT NULL, parent_event_id INTEGER DEFAULT NULL, created_by INTEGER DEFAULT NULL, updated_by INTEGER DEFAULT NULL, stage_order_id INTEGER DEFAULT NULL, helper_einlass_eins_id INTEGER DEFAULT NULL, helper_einlass_zwei_id INTEGER DEFAULT NULL, helper_kasse_id INTEGER DEFAULT NULL, helper_springer_eins_id INTEGER DEFAULT NULL, helper_springer_zwei_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO klo_ki_event (id, name, begin_at, end_at, anzahl_artists, is_best_benoetigt, is_licht_benoetigt, bemerkung, is_fixed, is_ton_benoetigt, helper_required, is_full_day, created_at, updated_at, art_id, kategorie_id, kontakt_id, best_plan_id, room_id, parent_event_id, created_by, updated_by, stage_order_id, helper_einlass_eins_id, helper_einlass_zwei_id, helper_kasse_id, helper_springer_eins_id, helper_springer_zwei_id) SELECT id, name, begin_at, end_at, anzahl_artists, is_best_benoetigt, is_licht_benoetigt, bemerkung, is_fixed, is_ton_benoetigt, helper_required, is_full_day, created_at, updated_at, art_id, kategorie_id, kontakt_id, best_plan_id, room_id, parent_event_id, created_by, updated_by, stage_order_id, helper_einlass_eins_id, helper_einlass_zwei_id, helper_kasse_id, helper_springer_eins_id, helper_springer_zwei_id FROM __temp__klo_ki_event');
        $this->addSql('DROP TABLE __temp__klo_ki_event');
    }
}
