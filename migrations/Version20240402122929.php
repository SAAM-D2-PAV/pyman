<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402122929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14B03A8386');
        $this->addSql('DROP TABLE note');
        $this->addSql('ALTER TABLE equipment CHANGE model model VARCHAR(255) NOT NULL, CHANGE brand brand VARCHAR(255) NOT NULL, CHANGE specifications specifications LONGTEXT DEFAULT NULL, CHANGE main_picture main_picture VARCHAR(255) NOT NULL, CHANGE note note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE location CHANGE ministry ministry VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE log_event RENAME INDEX fk_1bd0e1fa166d1f9c TO IDX_1BD0E1FA166D1F9C');
        $this->addSql('ALTER TABLE log_event RENAME INDEX fk_1bd0e1fa8db60186 TO IDX_1BD0E1FA8DB60186');
        $this->addSql('ALTER TABLE log_event RENAME INDEX fk_1bd0e1fab03a8386 TO IDX_1BD0E1FAB03A8386');
        $this->addSql('ALTER TABLE project CHANGE delivery_date delivery_date DATETIME NOT NULL, CHANGE note note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE rented_equipment CHANGE start_date start_date DATE NOT NULL');
        $this->addSql('ALTER TABLE task CHANGE start_date start_date DATE NOT NULL, CHANGE end_date end_date DATE NOT NULL, CHANGE start_hour start_hour TIME NOT NULL, CHANGE end_hour end_hour TIME NOT NULL');
        $this->addSql('ALTER TABLE task_category CHANGE color color LONGTEXT NOT NULL, CHANGE text_color text_color LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', allow_update_from_all TINYINT(1) NOT NULL, INDEX IDX_CFBDFA14B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task_category CHANGE color color VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE text_color text_color VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE location CHANGE ministry ministry VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE project CHANGE delivery_date delivery_date DATETIME DEFAULT NULL, CHANGE note note TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment CHANGE model model VARCHAR(255) DEFAULT NULL, CHANGE brand brand VARCHAR(255) DEFAULT NULL, CHANGE specifications specifications TINYTEXT DEFAULT NULL, CHANGE main_picture main_picture VARCHAR(255) DEFAULT NULL, CHANGE note note TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE start_date start_date DATE DEFAULT NULL, CHANGE end_date end_date DATE DEFAULT NULL, CHANGE start_hour start_hour TIME DEFAULT NULL, CHANGE end_hour end_hour TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE log_event RENAME INDEX idx_1bd0e1fab03a8386 TO FK_1BD0E1FAB03A8386');
        $this->addSql('ALTER TABLE log_event RENAME INDEX idx_1bd0e1fa166d1f9c TO FK_1BD0E1FA166D1F9C');
        $this->addSql('ALTER TABLE log_event RENAME INDEX idx_1bd0e1fa8db60186 TO FK_1BD0E1FA8DB60186');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE rented_equipment CHANGE start_date start_date DATE DEFAULT NULL');
    }
}
