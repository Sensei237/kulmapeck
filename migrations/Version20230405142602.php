<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405142602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe ADD sous_systeme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96D21722C4 FOREIGN KEY (sous_systeme_id) REFERENCES sous_systeme (id)');
        $this->addSql('CREATE INDEX IDX_8F87BF96D21722C4 ON classe (sous_systeme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96D21722C4');
        $this->addSql('DROP INDEX IDX_8F87BF96D21722C4 ON classe');
        $this->addSql('ALTER TABLE classe DROP sous_systeme_id');
    }
}
