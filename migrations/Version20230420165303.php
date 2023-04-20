<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230420165303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD skill_level_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C1D192655 FOREIGN KEY (skill_level_id) REFERENCES skill_level (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C1D192655 ON cours (skill_level_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C1D192655');
        $this->addSql('DROP INDEX IDX_FDCA8C9C1D192655 ON cours');
        $this->addSql('ALTER TABLE cours DROP skill_level_id');
    }
}
