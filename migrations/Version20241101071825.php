<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241101071825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizer ADD manager_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE organizer ADD CONSTRAINT FK_99D47173783E3463 FOREIGN KEY (manager_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_99D47173783E3463 ON organizer (manager_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizer DROP FOREIGN KEY FK_99D47173783E3463');
        $this->addSql('DROP INDEX IDX_99D47173783E3463 ON organizer');
        $this->addSql('ALTER TABLE organizer DROP manager_id');
    }
}
