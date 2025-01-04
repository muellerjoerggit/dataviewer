<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103200926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE version (id VARCHAR(255) NOT NULL, predecessor_id VARCHAR(255) DEFAULT NULL, successor_id VARCHAR(255) DEFAULT NULL, label VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF1CD3C368C90015 ON version (predecessor_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF1CD3C37323E667 ON version (successor_id)');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C368C90015 FOREIGN KEY (predecessor_id) REFERENCES version (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37323E667 FOREIGN KEY (successor_id) REFERENCES version (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE client ADD version_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404554BBC2705 FOREIGN KEY (version_id) REFERENCES version (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74404554BBC2705 ON client (version_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C74404554BBC2705');
        $this->addSql('ALTER TABLE version DROP CONSTRAINT FK_BF1CD3C368C90015');
        $this->addSql('ALTER TABLE version DROP CONSTRAINT FK_BF1CD3C37323E667');
        $this->addSql('DROP TABLE version');
        $this->addSql('DROP INDEX UNIQ_C74404554BBC2705');
        $this->addSql('ALTER TABLE client DROP version_id');
    }
}
