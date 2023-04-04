<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403220831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam ADD classe_id INT DEFAULT NULL, ADD category_id INT DEFAULT NULL, ADD duration VARCHAR(50) NOT NULL, ADD image_file VARCHAR(255) DEFAULT NULL, ADD language VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C68F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C612469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_38BBA6C68F5EA509 ON exam (classe_id)');
        $this->addSql('CREATE INDEX IDX_38BBA6C612469DE2 ON exam (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C68F5EA509');
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C612469DE2');
        $this->addSql('DROP INDEX IDX_38BBA6C68F5EA509 ON exam');
        $this->addSql('DROP INDEX IDX_38BBA6C612469DE2 ON exam');
        $this->addSql('ALTER TABLE exam DROP classe_id, DROP category_id, DROP duration, DROP image_file, DROP language');
    }
}
