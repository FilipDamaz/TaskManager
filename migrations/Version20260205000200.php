<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Recreate users table with external_id and profile fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS users');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, external_id INT NOT NULL, name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, address JSONB DEFAULT NULL, company JSONB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_users_email ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX uniq_users_external_id ON users (external_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
