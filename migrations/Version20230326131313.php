<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326131313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, duree SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, abonnement_id INT DEFAULT NULL, cours_id INT DEFAULT NULL, paid_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_expired TINYINT(1) NOT NULL, expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6D28840DA6CC7B2 (eleve_id), INDEX IDX_6D28840DF1D74413 (abonnement_id), INDEX IDX_6D28840D7ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DF1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA6CC7B2');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DF1D74413');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7ECF78B0');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE payment');
    }
}
