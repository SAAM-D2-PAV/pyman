<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510134258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment ADD upload_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment DROP upload_name');
        $this->addSql('ALTER TABLE rented_equipment DROP FOREIGN KEY FK_D51D08B6B03A8386');
        $this->addSql('ALTER TABLE rented_equipment DROP FOREIGN KEY FK_D51D08B6896DBBDE');
        $this->addSql('DROP INDEX IDX_D51D08B6B03A8386 ON rented_equipment');
        $this->addSql('DROP INDEX IDX_D51D08B6896DBBDE ON rented_equipment');
        $this->addSql('ALTER TABLE rented_equipment CHANGE start_date start_date DATE DEFAULT NULL');
    }
}
