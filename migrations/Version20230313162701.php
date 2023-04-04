<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230313162701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, INDEX IDX_E8FF75CC7ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, video_url VARCHAR(255) DEFAULT NULL, mp4_file VARCHAR(255) DEFAULT NULL, web_mfile VARCHAR(255) DEFAULT NULL, ogg_file VARCHAR(255) DEFAULT NULL, image_file VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6A2CA10C7ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_E8FF75CC7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE chapitre DROP objectifs');
        $this->addSql('ALTER TABLE cours CHANGE objectifs content LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faq DROP FOREIGN KEY FK_E8FF75CC7ECF78B0');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C7ECF78B0');
        $this->addSql('DROP TABLE faq');
        $this->addSql('DROP TABLE media');
        $this->addSql('ALTER TABLE chapitre ADD objectifs LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE cours CHANGE content objectifs LONGTEXT NOT NULL');
    }
}
