<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210406101805 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, location_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_date DATETIME NOT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, start_hour TIME DEFAULT NULL, end_hour TIME DEFAULT NULL, attachment VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_527EDB2512469DE2 (category_id), INDEX IDX_527EDB2564D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_equipment (task_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_AC1218D28DB60186 (task_id), INDEX IDX_AC1218D2517FE9FE (equipment_id), PRIMARY KEY(task_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2512469DE2 FOREIGN KEY (category_id) REFERENCES task_category (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2564D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE task_equipment ADD CONSTRAINT FK_AC1218D28DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_equipment ADD CONSTRAINT FK_AC1218D2517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_category CHANGE description description LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_equipment DROP FOREIGN KEY FK_AC1218D28DB60186');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_equipment');
        $this->addSql('ALTER TABLE task_category CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
