<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405184430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_setting CHANGE site_name site_name VARCHAR(150) DEFAULT NULL, CHANGE site_email site_email VARCHAR(100) DEFAULT NULL, CHANGE site_description site_description LONGTEXT DEFAULT NULL, CHANGE contact_phone contact_phone VARCHAR(50) DEFAULT NULL, CHANGE support_email support_email VARCHAR(100) DEFAULT NULL, CHANGE contact_address contact_address LONGTEXT DEFAULT NULL, CHANGE main_site_url main_site_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_setting CHANGE site_name site_name VARCHAR(150) NOT NULL, CHANGE site_email site_email VARCHAR(100) NOT NULL, CHANGE site_description site_description LONGTEXT NOT NULL, CHANGE contact_phone contact_phone VARCHAR(50) NOT NULL, CHANGE support_email support_email VARCHAR(100) NOT NULL, CHANGE contact_address contact_address LONGTEXT NOT NULL, CHANGE main_site_url main_site_url VARCHAR(255) NOT NULL');
    }
}
