<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210922123709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD url VARCHAR(2000) DEFAULT NULL, ADD rating VARCHAR(255) DEFAULT NULL, ADD note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE start_date start_date DATE NOT NULL, CHANGE end_date end_date DATE NOT NULL, CHANGE start_hour start_hour TIME NOT NULL, CHANGE end_hour end_hour TIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP url, DROP rating, DROP note');
        $this->addSql('ALTER TABLE task CHANGE start_date start_date DATE DEFAULT NULL, CHANGE end_date end_date DATE DEFAULT NULL, CHANGE start_hour start_hour TIME DEFAULT NULL, CHANGE end_hour end_hour TIME DEFAULT NULL');
    }
}
