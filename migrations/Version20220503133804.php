<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220503133804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rented_equipment (id INT AUTO_INCREMENT NOT NULL, applicant_id INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_D51D08B697139001 (applicant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rented_equipment ADD CONSTRAINT FK_D51D08B697139001 FOREIGN KEY (applicant_id) REFERENCES applicant (id)');
        $this->addSql('ALTER TABLE equipment ADD rent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583E5FD6250 FOREIGN KEY (rent_id) REFERENCES rented_equipment (id)');
        $this->addSql('CREATE INDEX IDX_D338D583E5FD6250 ON equipment (rent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D583E5FD6250');
        $this->addSql('DROP TABLE rented_equipment');
        $this->addSql('DROP INDEX IDX_D338D583E5FD6250 ON equipment');
        $this->addSql('ALTER TABLE equipment DROP rent_id');
    }
}
