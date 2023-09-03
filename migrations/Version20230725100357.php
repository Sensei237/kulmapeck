<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230725100357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE retrait (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, payment_method_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, is_done TINYINT(1) NOT NULL, numero_telephone VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D9846A51A76ED395 (user_id), INDEX IDX_D9846A515AA1164F (payment_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE retrait ADD CONSTRAINT FK_D9846A51A76ED395 FOREIGN KEY (user_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('ALTER TABLE retrait ADD CONSTRAINT FK_D9846A515AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE retrait DROP FOREIGN KEY FK_D9846A51A76ED395');
        $this->addSql('ALTER TABLE retrait DROP FOREIGN KEY FK_D9846A515AA1164F');
        $this->addSql('DROP TABLE retrait');
    }
}
