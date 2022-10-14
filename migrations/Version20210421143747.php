<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421143747 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_527EDB25B03A8386 ON task (created_by_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25896DBBDE ON task (updated_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25B03A8386');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25896DBBDE');
        $this->addSql('DROP INDEX IDX_527EDB25B03A8386 ON task');
        $this->addSql('DROP INDEX IDX_527EDB25896DBBDE ON task');
        $this->addSql('ALTER TABLE task DROP created_by_id, DROP updated_by_id, DROP updated_at');
    }
}
