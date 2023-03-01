<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230301132806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment CHANGE model model VARCHAR(255) NOT NULL, CHANGE brand brand VARCHAR(255) NOT NULL, CHANGE main_picture main_picture VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE task_category CHANGE color color LONGTEXT NOT NULL, CHANGE text_color text_color LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user ADD task_owner TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_category CHANGE color color VARCHAR(255) NOT NULL, CHANGE text_color text_color VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE equipment CHANGE model model VARCHAR(255) DEFAULT NULL, CHANGE brand brand VARCHAR(255) DEFAULT NULL, CHANGE main_picture main_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP task_owner');
    }
}
