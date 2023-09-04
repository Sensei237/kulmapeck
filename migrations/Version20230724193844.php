<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230724193844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE like_message_forum (id INT AUTO_INCREMENT NOT NULL, forum_message_id INT NOT NULL, membre_id INT NOT NULL, INDEX IDX_983F8859D14CAE6C (forum_message_id), INDEX IDX_983F88596A99F74A (membre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE like_message_forum ADD CONSTRAINT FK_983F8859D14CAE6C FOREIGN KEY (forum_message_id) REFERENCES forum_message (id)');
        $this->addSql('ALTER TABLE like_message_forum ADD CONSTRAINT FK_983F88596A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE like_message_forum DROP FOREIGN KEY FK_983F8859D14CAE6C');
        $this->addSql('ALTER TABLE like_message_forum DROP FOREIGN KEY FK_983F88596A99F74A');
        $this->addSql('DROP TABLE like_message_forum');
    }
}
