<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241101121725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create ticket table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ticket (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            event_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            price INT NOT NULL,
            type ENUM(\'BALCONY\', \'BOX SEATS\', \'FLOOR\', \'CLUB LEVEL\', \'VIP\', \'GROUND FLOOR\', \'MEZZANINE\') NOT NULL COMMENT \'(DC2Type:ticket_type)\',
            number_available_tickets INT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            deleted_at DATETIME DEFAULT NULL,
            INDEX IDX_97A0ADA371F7E88B (event_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA371F7E88B');
        $this->addSql('DROP TABLE ticket');
    }
}
