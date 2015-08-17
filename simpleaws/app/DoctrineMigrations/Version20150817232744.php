<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150817232744 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX unique_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__schedule AS SELECT rowid, quantity, create_at, schedule_at, run_at, as_group FROM schedule');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('CREATE TABLE schedule (rowid VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, create_at DATETIME NOT NULL, schedule_at DATETIME NOT NULL, run_at DATETIME DEFAULT NULL, as_group VARCHAR(255) NOT NULL, PRIMARY KEY(rowid))');
        $this->addSql('INSERT INTO schedule (rowid, quantity, create_at, schedule_at, run_at, as_group) SELECT rowid, quantity, create_at, schedule_at, run_at, as_group FROM __temp__schedule');
        $this->addSql('DROP TABLE __temp__schedule');
        $this->addSql('CREATE UNIQUE INDEX unique_idx ON schedule (schedule_at)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX unique_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__schedule AS SELECT rowid, as_group, quantity, create_at, schedule_at, run_at FROM schedule');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('CREATE TABLE schedule (rowid INTEGER NOT NULL, as_group VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, create_at DATETIME NOT NULL, schedule_at DATETIME NOT NULL, run_at DATETIME DEFAULT NULL, PRIMARY KEY(rowid))');
        $this->addSql('INSERT INTO schedule (rowid, as_group, quantity, create_at, schedule_at, run_at) SELECT rowid, as_group, quantity, create_at, schedule_at, run_at FROM __temp__schedule');
        $this->addSql('DROP TABLE __temp__schedule');
        $this->addSql('CREATE UNIQUE INDEX unique_idx ON schedule (schedule_at)');
    }
}
