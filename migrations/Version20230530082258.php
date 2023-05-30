<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230530082258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evaluation_resultat (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT NOT NULL, eleve_id INT NOT NULL, contents LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_EA9524C4456C5646 (evaluation_id), INDEX IDX_EA9524C4A6CC7B2 (eleve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation_resultat ADD CONSTRAINT FK_EA9524C4456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE evaluation_resultat ADD CONSTRAINT FK_EA9524C4A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation_resultat DROP FOREIGN KEY FK_EA9524C4456C5646');
        $this->addSql('ALTER TABLE evaluation_resultat DROP FOREIGN KEY FK_EA9524C4A6CC7B2');
        $this->addSql('DROP TABLE evaluation_resultat');
    }
}
