<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205000400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tasks and event_store tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tasks (id VARCHAR(36) NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, status VARCHAR(32) NOT NULL, assignee_id VARCHAR(36) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_tasks_assignee ON tasks (assignee_id)');
        $this->addSql('CREATE TABLE event_store (id SERIAL NOT NULL, aggregate_type VARCHAR(64) NOT NULL, aggregate_id VARCHAR(36) NOT NULL, event_type VARCHAR(128) NOT NULL, payload JSONB NOT NULL, occurred_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_event_store_aggregate ON event_store (aggregate_type, aggregate_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE event_store');
        $this->addSql('DROP TABLE tasks');
    }
}
