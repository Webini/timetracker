<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210220231424 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assigned_user ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE assigned_user ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE assigned_user ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE assigned_user ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE project ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE project ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE project ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE project ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task_provider ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE task_provider ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task_provider ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE task_provider ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task_timer ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE task_timer ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task_timer ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE task_timer ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE users ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE users ALTER updated_at DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE assigned_user ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE assigned_user ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE assigned_user ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE assigned_user ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE project ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE project ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE project ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE project ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task_provider ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task_provider ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task_provider ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task_provider ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE users ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE users ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task_timer ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task_timer ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE task_timer ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task_timer ALTER updated_at DROP DEFAULT');
    }
}
