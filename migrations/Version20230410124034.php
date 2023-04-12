<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230410124034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quiz_lost (id INT AUTO_INCREMENT NOT NULL, cours_id INT DEFAULT NULL, chapitre_id INT DEFAULT NULL, eleve_id INT NOT NULL, attempt SMALLINT NOT NULL, last_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', next_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_ok TINYINT(1) NOT NULL, INDEX IDX_8AB478F17ECF78B0 (cours_id), INDEX IDX_8AB478F11FBEEF7B (chapitre_id), INDEX IDX_8AB478F1A6CC7B2 (eleve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quiz_lost ADD CONSTRAINT FK_8AB478F17ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE quiz_lost ADD CONSTRAINT FK_8AB478F11FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('ALTER TABLE quiz_lost ADD CONSTRAINT FK_8AB478F1A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_lost DROP FOREIGN KEY FK_8AB478F17ECF78B0');
        $this->addSql('ALTER TABLE quiz_lost DROP FOREIGN KEY FK_8AB478F11FBEEF7B');
        $this->addSql('ALTER TABLE quiz_lost DROP FOREIGN KEY FK_8AB478F1A6CC7B2');
        $this->addSql('DROP TABLE quiz_lost');
    }
}
