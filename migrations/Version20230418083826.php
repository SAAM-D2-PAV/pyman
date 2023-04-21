<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418083826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', allow_update_from_all TINYINT(1) NOT NULL, INDEX IDX_CFBDFA14B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE equipment CHANGE model model VARCHAR(255) NOT NULL, CHANGE brand brand VARCHAR(255) NOT NULL, CHANGE specifications specifications LONGTEXT DEFAULT NULL, CHANGE main_picture main_picture VARCHAR(255) NOT NULL, CHANGE note note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE location CHANGE ministry ministry VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE log_event DROP FOREIGN KEY FK_1BD0E1FA8DB60186');
        $this->addSql('ALTER TABLE log_event DROP FOREIGN KEY FK_1BD0E1FA166D1F9C');
        $this->addSql('ALTER TABLE log_event DROP FOREIGN KEY FK_1BD0E1FAB03A8386');
        $this->addSql('DROP INDEX fk_1bd0e1fa166d1f9c ON log_event');
        $this->addSql('CREATE INDEX IDX_1BD0E1FA166D1F9C ON log_event (project_id)');
        $this->addSql('DROP INDEX fk_1bd0e1fa8db60186 ON log_event');
        $this->addSql('CREATE INDEX IDX_1BD0E1FA8DB60186 ON log_event (task_id)');
        $this->addSql('DROP INDEX fk_1bd0e1fab03a8386 ON log_event');
        $this->addSql('CREATE INDEX IDX_1BD0E1FAB03A8386 ON log_event (created_by_id)');
        $this->addSql('ALTER TABLE log_event ADD CONSTRAINT FK_1BD0E1FA8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE log_event ADD CONSTRAINT FK_1BD0E1FA166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE log_event ADD CONSTRAINT FK_1BD0E1FAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE896DBBDE');
        $this->addSql('DROP INDEX IDX_2FB3D0EE896DBBDE ON project');
        $this->addSql('ALTER TABLE project DROP updated_by_id, CHANGE delivery_date delivery_date DATETIME NOT NULL, CHANGE note note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE rented_equipment CHANGE start_date start_date DATE NOT NULL');
        $this->addSql('ALTER TABLE task CHANGE start_date start_date DATE NOT NULL, CHANGE end_date end_date DATE NOT NULL, CHANGE start_hour start_hour TIME NOT NULL, CHANGE end_hour end_hour TIME NOT NULL');
        $this->addSql('ALTER TABLE task_category CHANGE color color LONGTEXT NOT NULL, CHANGE text_color text_color LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14B03A8386');
        $this->addSql('DROP TABLE note');
        $this->addSql('ALTER TABLE task_category CHANGE color color VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE text_color text_color VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE location CHANGE ministry ministry VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE project ADD updated_by_id INT DEFAULT NULL, CHANGE delivery_date delivery_date DATETIME DEFAULT NULL, CHANGE note note TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE896DBBDE ON project (updated_by_id)');
        $this->addSql('ALTER TABLE equipment CHANGE model model VARCHAR(255) DEFAULT NULL, CHANGE brand brand VARCHAR(255) DEFAULT NULL, CHANGE specifications specifications TINYTEXT DEFAULT NULL, CHANGE main_picture main_picture VARCHAR(255) DEFAULT NULL, CHANGE note note TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE start_date start_date DATE DEFAULT NULL, CHANGE end_date end_date DATE DEFAULT NULL, CHANGE start_hour start_hour TIME DEFAULT NULL, CHANGE end_hour end_hour TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE log_event DROP FOREIGN KEY FK_1BD0E1FA166D1F9C');
        $this->addSql('ALTER TABLE log_event DROP FOREIGN KEY FK_1BD0E1FA8DB60186');
        $this->addSql('ALTER TABLE log_event DROP FOREIGN KEY FK_1BD0E1FAB03A8386');
        $this->addSql('DROP INDEX idx_1bd0e1fab03a8386 ON log_event');
        $this->addSql('CREATE INDEX FK_1BD0E1FAB03A8386 ON log_event (created_by_id)');
        $this->addSql('DROP INDEX idx_1bd0e1fa166d1f9c ON log_event');
        $this->addSql('CREATE INDEX FK_1BD0E1FA166D1F9C ON log_event (project_id)');
        $this->addSql('DROP INDEX idx_1bd0e1fa8db60186 ON log_event');
        $this->addSql('CREATE INDEX FK_1BD0E1FA8DB60186 ON log_event (task_id)');
        $this->addSql('ALTER TABLE log_event ADD CONSTRAINT FK_1BD0E1FA166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE log_event ADD CONSTRAINT FK_1BD0E1FA8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE log_event ADD CONSTRAINT FK_1BD0E1FAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rented_equipment CHANGE start_date start_date DATE DEFAULT NULL');
    }
}
