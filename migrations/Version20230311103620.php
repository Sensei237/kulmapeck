<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230311103620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant ADD diplome VARCHAR(255) NOT NULL, ADD recto_cni VARCHAR(255) NOT NULL, ADD verso_cni VARCHAR(255) NOT NULL, ADD selfie_cni VARCHAR(255) NOT NULL, ADD emploi_du_temps VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP diplome, DROP recto_cni, DROP verso_cni, DROP selfie_cni, DROP emploi_du_temps');
    }
}
