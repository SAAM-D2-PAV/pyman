<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220503143429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rented_equipment ADD created_by_id INT NOT NULL, ADD updated_by_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE rented_equipment ADD CONSTRAINT FK_D51D08B6B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rented_equipment ADD CONSTRAINT FK_D51D08B6896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D51D08B6B03A8386 ON rented_equipment (created_by_id)');
        $this->addSql('CREATE INDEX IDX_D51D08B6896DBBDE ON rented_equipment (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rented_equipment DROP FOREIGN KEY FK_D51D08B6B03A8386');
        $this->addSql('ALTER TABLE rented_equipment DROP FOREIGN KEY FK_D51D08B6896DBBDE');
        $this->addSql('DROP INDEX IDX_D51D08B6B03A8386 ON rented_equipment');
        $this->addSql('DROP INDEX IDX_D51D08B6896DBBDE ON rented_equipment');
        $this->addSql('ALTER TABLE rented_equipment DROP created_by_id, DROP updated_by_id, DROP created_at, DROP updated_at');
    }
}
