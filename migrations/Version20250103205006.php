<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103205006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE background_task (id SERIAL NOT NULL, name VARCHAR(100) NOT NULL, status SMALLINT NOT NULL, description VARCHAR(255) DEFAULT NULL, start TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, "end" TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, progress TEXT DEFAULT NULL, terminate BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE file (id SERIAL NOT NULL, server_filename VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, type INT NOT NULL, mimetype VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE task_configuration (id SERIAL NOT NULL, task_id INT NOT NULL, command VARCHAR(100) NOT NULL, configuration TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E0F568868DB60186 ON task_configuration (task_id)');
        $this->addSql('CREATE TABLE task_result (id SERIAL NOT NULL, task_id INT NOT NULL, background_task_id INT NOT NULL, result TEXT DEFAULT NULL, type INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_28C345C08DB60186 ON task_result (task_id)');
        $this->addSql('CREATE INDEX IDX_28C345C0497210AC ON task_result (background_task_id)');
        $this->addSql('ALTER TABLE task_configuration ADD CONSTRAINT FK_E0F568868DB60186 FOREIGN KEY (task_id) REFERENCES background_task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_result ADD CONSTRAINT FK_28C345C08DB60186 FOREIGN KEY (task_id) REFERENCES background_task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_result ADD CONSTRAINT FK_28C345C0497210AC FOREIGN KEY (background_task_id) REFERENCES background_task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task_configuration DROP CONSTRAINT FK_E0F568868DB60186');
        $this->addSql('ALTER TABLE task_result DROP CONSTRAINT FK_28C345C08DB60186');
        $this->addSql('ALTER TABLE task_result DROP CONSTRAINT FK_28C345C0497210AC');
        $this->addSql('DROP TABLE background_task');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE task_configuration');
        $this->addSql('DROP TABLE task_result');
    }
}
