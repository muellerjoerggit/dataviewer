<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250125231753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_result DROP CONSTRAINT fk_28c345c0497210ac');
        $this->addSql('DROP INDEX idx_28c345c0497210ac');
        $this->addSql('DROP INDEX uniq_28c345c08db60186');
        $this->addSql('ALTER TABLE task_result DROP background_task_id');
        $this->addSql('CREATE INDEX IDX_28C345C08DB60186 ON task_result (task_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_28C345C08DB60186');
        $this->addSql('ALTER TABLE task_result ADD background_task_id INT NOT NULL');
        $this->addSql('ALTER TABLE task_result ADD CONSTRAINT fk_28c345c0497210ac FOREIGN KEY (background_task_id) REFERENCES background_task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_28c345c0497210ac ON task_result (background_task_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_28c345c08db60186 ON task_result (task_id)');
    }
}
