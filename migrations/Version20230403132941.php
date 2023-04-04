<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403132941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam ADD user_id INT NOT NULL, ADD published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD is_published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C6A76ED395 FOREIGN KEY (user_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('CREATE INDEX IDX_38BBA6C6A76ED395 ON exam (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C6A76ED395');
        $this->addSql('DROP INDEX IDX_38BBA6C6A76ED395 ON exam');
        $this->addSql('ALTER TABLE exam DROP user_id, DROP published_at, DROP is_published');
    }
}
