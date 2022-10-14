<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210423095430 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD request_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEF7F09C21 FOREIGN KEY (request_by_id) REFERENCES applicant (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEF7F09C21 ON project (request_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEF7F09C21');
        $this->addSql('DROP INDEX IDX_2FB3D0EEF7F09C21 ON project');
        $this->addSql('ALTER TABLE project DROP request_by_id');
    }
}
