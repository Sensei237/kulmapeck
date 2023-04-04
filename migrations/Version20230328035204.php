<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230328035204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_message ADD forum_message_id INT DEFAULT NULL, ADD is_answer TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_message ADD CONSTRAINT FK_47717D0ED14CAE6C FOREIGN KEY (forum_message_id) REFERENCES forum_message (id)');
        $this->addSql('CREATE INDEX IDX_47717D0ED14CAE6C ON forum_message (forum_message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_message DROP FOREIGN KEY FK_47717D0ED14CAE6C');
        $this->addSql('DROP INDEX IDX_47717D0ED14CAE6C ON forum_message');
        $this->addSql('ALTER TABLE forum_message DROP forum_message_id, DROP is_answer');
    }
}
