<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904073606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE dummy (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            hobby VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            created_at TIMESTAMP NOT NULL
        )');
       
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE dummy');

    }
}
