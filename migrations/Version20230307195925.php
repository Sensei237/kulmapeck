<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307195925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD montant_abonnement INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personne CHANGE lieu_naissance lieu_naissance VARCHAR(255) DEFAULT NULL, CHANGE telephone telephone VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP montant_abonnement');
        $this->addSql('ALTER TABLE personne CHANGE lieu_naissance lieu_naissance VARCHAR(255) NOT NULL, CHANGE telephone telephone VARCHAR(255) NOT NULL');
    }
}