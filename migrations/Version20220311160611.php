<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220311160611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment CHANGE specifications specifications LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE log_event ADD rated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE log_event ADD CONSTRAINT FK_1BD0E1FA284C0C5B FOREIGN KEY (rated_by_id) REFERENCES applicant (id)');
        $this->addSql('CREATE INDEX IDX_1BD0E1FA284C0C5B ON log_event (rated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment CHANGE specifications specifications TINYTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE log_event DROP FOREIGN KEY FK_1BD0E1FA284C0C5B');
        $this->addSql('DROP INDEX IDX_1BD0E1FA284C0C5B ON log_event');
        $this->addSql('ALTER TABLE log_event DROP rated_by_id');
    }
}
