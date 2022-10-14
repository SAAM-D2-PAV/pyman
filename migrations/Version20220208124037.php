<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220208124037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_rate_by_applicant (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, applicant_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, note VARCHAR(255) NOT NULL, comment LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_5584147C166D1F9C (project_id), INDEX IDX_5584147C97139001 (applicant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_rate_by_applicant ADD CONSTRAINT FK_5584147C166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE project_rate_by_applicant ADD CONSTRAINT FK_5584147C97139001 FOREIGN KEY (applicant_id) REFERENCES applicant (id)');
        $this->addSql('ALTER TABLE project CHANGE delivery_date delivery_date DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE project_rate_by_applicant');
        $this->addSql('ALTER TABLE project CHANGE delivery_date delivery_date DATETIME DEFAULT NULL');
    }
}
