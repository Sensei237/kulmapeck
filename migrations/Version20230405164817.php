<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405164817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site_setting (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(150) NOT NULL, site_copyrights VARCHAR(200) DEFAULT NULL, site_email VARCHAR(100) NOT NULL, site_description LONGTEXT NOT NULL, contact_phone VARCHAR(50) NOT NULL, support_email VARCHAR(100) NOT NULL, contact_address LONGTEXT NOT NULL, main_site_url VARCHAR(255) NOT NULL, is_maintenance_mode TINYINT(1) DEFAULT NULL, maintenance_text LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE site_setting');
    }
}
