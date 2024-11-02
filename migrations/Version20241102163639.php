<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241102163639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create event and ticket tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            organizer_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            name VARCHAR(255) NOT NULL,
            date DATETIME NOT NULL,
            type ENUM(\'CARNIVAL\', \'FAIR\', \'CONCERT\', \'FESTIVAL\', \'PARADE\', \'THEATER_PERFORMANCE\') NOT NULL COMMENT \'(DC2Type:event_type)\',
            city VARCHAR(255) NOT NULL,
            country VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            deleted_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_3BAE0AA7876C4DDA (organizer_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('CREATE TABLE ticket (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            event_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            price INT NOT NULL,
            type ENUM(\'BALCONY\', \'BOX SEATS\', \'FLOOR\', \'CLUB LEVEL\', \'VIP\', \'GROUND FLOOR\', \'MEZZANINE\') NOT NULL COMMENT \'(DC2Type:ticket_type)\',
            number_available_tickets INT NOT NULL,
            deleted_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_97A0ADA371F7E88B (event_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7876C4DDA FOREIGN KEY (organizer_id) REFERENCES organizer (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7876C4DDA');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA371F7E88B');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE ticket');
    }
}
