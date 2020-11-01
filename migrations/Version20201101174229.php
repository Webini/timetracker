<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201101174229 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE assigned_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_time_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE assigned_user (id INT NOT NULL, assigned_id INT NOT NULL, project_id INT NOT NULL, permissions INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64EB2CB0E1501A05 ON assigned_user (assigned_id)');
        $this->addSql('CREATE INDEX IDX_64EB2CB0166D1F9C ON assigned_user (project_id)');
        $this->addSql('CREATE TABLE project (id INT NOT NULL, task_provider_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, guid UUID NOT NULL, provider_configuration JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE2B6FCFB2 ON project (guid)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE4C260598 ON project (task_provider_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEDE12AB56 ON project (created_by)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE16FE72E1 ON project (updated_by)');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, project_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, external_id VARCHAR(255) DEFAULT NULL, name VARCHAR(1024) NOT NULL, description TEXT DEFAULT NULL, archived BOOLEAN DEFAULT \'false\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25DE12AB56 ON task (created_by)');
        $this->addSql('CREATE INDEX IDX_527EDB2516FE72E1 ON task (updated_by)');
        $this->addSql('CREATE TABLE task_provider (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, configuration JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D20499D07E3C61F9 ON task_provider (owner_id)');
        $this->addSql('CREATE TABLE task_time (id INT NOT NULL, task_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, started_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, stopped_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, note TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1C270C3E8DB60186 ON task_time (task_id)');
        $this->addSql('CREATE INDEX IDX_1C270C3EDE12AB56 ON task_time (created_by)');
        $this->addSql('CREATE INDEX IDX_1C270C3E16FE72E1 ON task_time (updated_by)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, email_validated BOOLEAN DEFAULT \'false\' NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, roles INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE assigned_user ADD CONSTRAINT FK_64EB2CB0E1501A05 FOREIGN KEY (assigned_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assigned_user ADD CONSTRAINT FK_64EB2CB0166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE4C260598 FOREIGN KEY (task_provider_id) REFERENCES task_provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2516FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_provider ADD CONSTRAINT FK_D20499D07E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_time ADD CONSTRAINT FK_1C270C3E8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_time ADD CONSTRAINT FK_1C270C3EDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_time ADD CONSTRAINT FK_1C270C3E16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assigned_user DROP CONSTRAINT FK_64EB2CB0166D1F9C');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25166D1F9C');
        $this->addSql('ALTER TABLE task_time DROP CONSTRAINT FK_1C270C3E8DB60186');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE4C260598');
        $this->addSql('ALTER TABLE assigned_user DROP CONSTRAINT FK_64EB2CB0E1501A05');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EEDE12AB56');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE16FE72E1');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25DE12AB56');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB2516FE72E1');
        $this->addSql('ALTER TABLE task_provider DROP CONSTRAINT FK_D20499D07E3C61F9');
        $this->addSql('ALTER TABLE task_time DROP CONSTRAINT FK_1C270C3EDE12AB56');
        $this->addSql('ALTER TABLE task_time DROP CONSTRAINT FK_1C270C3E16FE72E1');
        $this->addSql('DROP SEQUENCE assigned_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_provider_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_time_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE assigned_user');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_provider');
        $this->addSql('DROP TABLE task_time');
        $this->addSql('DROP TABLE users');
    }
}
