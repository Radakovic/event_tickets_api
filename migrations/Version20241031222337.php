<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031222337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add timestampable fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user
            ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            ADD deleted_at DATETIME DEFAULT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP deleted_at, DROP created_at, DROP updated_at');
    }
}
