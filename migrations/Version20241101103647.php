<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241101103647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Event table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            organizer_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            name VARCHAR(255) NOT NULL,
            date DATETIME NOT NULL,
            type ENUM(\'CARNIVAL\', \'FAIR\', \'CONCERT\', \'FESTIVAL\', \'PARADE\', \'THEATER_PERFORMANCE\') NOT NULL COMMENT \'(DC2Type:event_type)\',
            location VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            INDEX IDX_3BAE0AA7876C4DDA (organizer_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7876C4DDA FOREIGN KEY (organizer_id) REFERENCES organizer (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7876C4DDA');
        $this->addSql('DROP TABLE event');
    }
}
