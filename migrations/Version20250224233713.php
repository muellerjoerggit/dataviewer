<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224233713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE background_task (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, status SMALLINT NOT NULL, description VARCHAR(255) DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, progress LONGTEXT DEFAULT NULL, terminate TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (client_id VARCHAR(100) NOT NULL, version_id VARCHAR(255) DEFAULT NULL, name VARCHAR(100) NOT NULL, database_name VARCHAR(100) NOT NULL, url VARCHAR(255) DEFAULT NULL, INDEX IDX_C74404554BBC2705 (version_id), PRIMARY KEY(client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, type INT NOT NULL, mimetype VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_configuration (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, command VARCHAR(100) NOT NULL, configuration LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_E0F568868DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_result (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, result LONGTEXT DEFAULT NULL, type INT NOT NULL, INDEX IDX_28C345C08DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE version (id VARCHAR(255) NOT NULL, predecessor_id VARCHAR(255) DEFAULT NULL, successor_id VARCHAR(255) DEFAULT NULL, label VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_BF1CD3C368C90015 (predecessor_id), UNIQUE INDEX UNIQ_BF1CD3C37323E667 (successor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404554BBC2705 FOREIGN KEY (version_id) REFERENCES version (id)');
        $this->addSql('ALTER TABLE task_configuration ADD CONSTRAINT FK_E0F568868DB60186 FOREIGN KEY (task_id) REFERENCES background_task (id)');
        $this->addSql('ALTER TABLE task_result ADD CONSTRAINT FK_28C345C08DB60186 FOREIGN KEY (task_id) REFERENCES background_task (id)');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C368C90015 FOREIGN KEY (predecessor_id) REFERENCES version (id)');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37323E667 FOREIGN KEY (successor_id) REFERENCES version (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404554BBC2705');
        $this->addSql('ALTER TABLE task_configuration DROP FOREIGN KEY FK_E0F568868DB60186');
        $this->addSql('ALTER TABLE task_result DROP FOREIGN KEY FK_28C345C08DB60186');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C368C90015');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37323E667');
        $this->addSql('DROP TABLE background_task');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE task_configuration');
        $this->addSql('DROP TABLE task_result');
        $this->addSql('DROP TABLE version');
    }
}
