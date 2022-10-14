<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210309101413 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipment_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, information LONGTEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment_category_equipment (equipment_category_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_31E8C0E1730469C5 (equipment_category_id), INDEX IDX_31E8C0E1517FE9FE (equipment_id), PRIMARY KEY(equipment_category_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE equipment_category_equipment ADD CONSTRAINT FK_31E8C0E1730469C5 FOREIGN KEY (equipment_category_id) REFERENCES equipment_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment_category_equipment ADD CONSTRAINT FK_31E8C0E1517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment_category_equipment DROP FOREIGN KEY FK_31E8C0E1730469C5');
        $this->addSql('DROP TABLE equipment_category');
        $this->addSql('DROP TABLE equipment_category_equipment');
    }
}
