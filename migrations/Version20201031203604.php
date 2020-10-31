<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201031203604 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE assigned_project_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE assigned_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE assigned_user (id INT NOT NULL, assigned_id INT NOT NULL, project_id INT NOT NULL, permissions INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64EB2CB0E1501A05 ON assigned_user (assigned_id)');
        $this->addSql('CREATE INDEX IDX_64EB2CB0166D1F9C ON assigned_user (project_id)');
        $this->addSql('ALTER TABLE assigned_user ADD CONSTRAINT FK_64EB2CB0E1501A05 FOREIGN KEY (assigned_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assigned_user ADD CONSTRAINT FK_64EB2CB0166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE assigned_project');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE assigned_user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE assigned_project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE assigned_project (id INT NOT NULL, assigned_id INT NOT NULL, project_id INT NOT NULL, permissions INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_252b6861e1501a05 ON assigned_project (assigned_id)');
        $this->addSql('CREATE INDEX idx_252b6861166d1f9c ON assigned_project (project_id)');
        $this->addSql('ALTER TABLE assigned_project ADD CONSTRAINT fk_252b6861e1501a05 FOREIGN KEY (assigned_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assigned_project ADD CONSTRAINT fk_252b6861166d1f9c FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE assigned_user');
    }
}
