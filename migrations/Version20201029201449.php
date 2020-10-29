<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029201449 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove owner from project';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP CONSTRAINT fk_2fb3d0ee7e3c61f9');
        $this->addSql('DROP INDEX idx_2fb3d0ee7e3c61f9');
        $this->addSql('ALTER TABLE project DROP owner_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT fk_2fb3d0ee7e3c61f9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2fb3d0ee7e3c61f9 ON project (owner_id)');
    }
}
