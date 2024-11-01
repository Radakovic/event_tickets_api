<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241101012131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create organizer table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE organizer (
                id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
                name VARCHAR(255) NOT NULL,
                city VARCHAR(255) NOT NULL,
                address VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                deleted_at DATETIME DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE organizer');
    }
}
