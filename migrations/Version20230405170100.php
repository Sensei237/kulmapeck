<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405170100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_setting (id INT AUTO_INCREMENT NOT NULL, template LONGTEXT NOT NULL, type VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_setting (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, notification_type_id INT DEFAULT NULL, INDEX IDX_8A6A322FA76ED395 (user_id), INDEX IDX_8A6A322FD0520624 (notification_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_setting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, link VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification_setting ADD CONSTRAINT FK_8A6A322FA76ED395 FOREIGN KEY (user_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('ALTER TABLE notification_setting ADD CONSTRAINT FK_8A6A322FD0520624 FOREIGN KEY (notification_type_id) REFERENCES notification_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_setting DROP FOREIGN KEY FK_8A6A322FA76ED395');
        $this->addSql('ALTER TABLE notification_setting DROP FOREIGN KEY FK_8A6A322FD0520624');
        $this->addSql('DROP TABLE email_setting');
        $this->addSql('DROP TABLE notification_setting');
        $this->addSql('DROP TABLE notification_type');
        $this->addSql('DROP TABLE social_setting');
    }
}
