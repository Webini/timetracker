<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201101213627 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_timer ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task_timer ADD CONSTRAINT FK_AD74C53A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AD74C53A7E3C61F9 ON task_timer (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task_timer DROP CONSTRAINT FK_AD74C53A7E3C61F9');
        $this->addSql('DROP INDEX IDX_AD74C53A7E3C61F9');
        $this->addSql('ALTER TABLE task_timer DROP owner_id');
    }
}
