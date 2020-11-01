<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201101180234 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE task_time_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE task_timer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_timer (id INT NOT NULL, task_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, started_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, stopped_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, note TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AD74C53A8DB60186 ON task_timer (task_id)');
        $this->addSql('CREATE INDEX IDX_AD74C53ADE12AB56 ON task_timer (created_by)');
        $this->addSql('CREATE INDEX IDX_AD74C53A16FE72E1 ON task_timer (updated_by)');
        $this->addSql('ALTER TABLE task_timer ADD CONSTRAINT FK_AD74C53A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_timer ADD CONSTRAINT FK_AD74C53ADE12AB56 FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_timer ADD CONSTRAINT FK_AD74C53A16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE task_time');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE task_timer_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE task_time_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_time (id INT NOT NULL, task_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, started_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, stopped_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, note TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_1c270c3e8db60186 ON task_time (task_id)');
        $this->addSql('CREATE INDEX idx_1c270c3ede12ab56 ON task_time (created_by)');
        $this->addSql('CREATE INDEX idx_1c270c3e16fe72e1 ON task_time (updated_by)');
        $this->addSql('ALTER TABLE task_time ADD CONSTRAINT fk_1c270c3e8db60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_time ADD CONSTRAINT fk_1c270c3ede12ab56 FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_time ADD CONSTRAINT fk_1c270c3e16fe72e1 FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE task_timer');
    }
}
