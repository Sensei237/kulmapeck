<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230401085500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant ADD discipline_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1A5522701 FOREIGN KEY (discipline_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_81A72FA1A5522701 ON enseignant (discipline_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1A5522701');
        $this->addSql('DROP INDEX IDX_81A72FA1A5522701 ON enseignant');
        $this->addSql('ALTER TABLE enseignant DROP discipline_id');
    }
}
