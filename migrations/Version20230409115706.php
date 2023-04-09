<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409115706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement_abonnement_item (abonnement_id INT NOT NULL, abonnement_item_id INT NOT NULL, INDEX IDX_FCF04D9BF1D74413 (abonnement_id), INDEX IDX_FCF04D9B723DEF36 (abonnement_item_id), PRIMARY KEY(abonnement_id, abonnement_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abonnement_item (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abonnement_abonnement_item ADD CONSTRAINT FK_FCF04D9BF1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abonnement_abonnement_item ADD CONSTRAINT FK_FCF04D9B723DEF36 FOREIGN KEY (abonnement_item_id) REFERENCES abonnement_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abonnement ADD is_recommended TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement_abonnement_item DROP FOREIGN KEY FK_FCF04D9BF1D74413');
        $this->addSql('ALTER TABLE abonnement_abonnement_item DROP FOREIGN KEY FK_FCF04D9B723DEF36');
        $this->addSql('DROP TABLE abonnement_abonnement_item');
        $this->addSql('DROP TABLE abonnement_item');
        $this->addSql('ALTER TABLE abonnement DROP is_recommended');
    }
}
