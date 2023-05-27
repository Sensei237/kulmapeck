<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230523075158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, matiere_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', duree SMALLINT NOT NULL, is_generated_random_questions TINYINT(1) NOT NULL, INDEX IDX_1323A575F46CD258 (matiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation_classe (evaluation_id INT NOT NULL, classe_id INT NOT NULL, INDEX IDX_45F88A61456C5646 (evaluation_id), INDEX IDX_45F88A618F5EA509 (classe_id), PRIMARY KEY(evaluation_id, classe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation_question (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT NOT NULL, question LONGTEXT NOT NULL, proposition1 LONGTEXT NOT NULL, propoition2 LONGTEXT NOT NULL, proposition3 LONGTEXT DEFAULT NULL, proposition4 LONGTEXT DEFAULT NULL, proposition_juste LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_BBB93068456C5646 (evaluation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575F46CD258 FOREIGN KEY (matiere_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE evaluation_classe ADD CONSTRAINT FK_45F88A61456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evaluation_classe ADD CONSTRAINT FK_45F88A618F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evaluation_question ADD CONSTRAINT FK_BBB93068456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575F46CD258');
        $this->addSql('ALTER TABLE evaluation_classe DROP FOREIGN KEY FK_45F88A61456C5646');
        $this->addSql('ALTER TABLE evaluation_classe DROP FOREIGN KEY FK_45F88A618F5EA509');
        $this->addSql('ALTER TABLE evaluation_question DROP FOREIGN KEY FK_BBB93068456C5646');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE evaluation_classe');
        $this->addSql('DROP TABLE evaluation_question');
    }
}
