<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230329213622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cours_payment_method (cours_id INT NOT NULL, payment_method_id INT NOT NULL, INDEX IDX_BC1479C67ECF78B0 (cours_id), INDEX IDX_BC1479C65AA1164F (payment_method_id), PRIMARY KEY(cours_id, payment_method_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_method (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cours_payment_method ADD CONSTRAINT FK_BC1479C67ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_payment_method ADD CONSTRAINT FK_BC1479C65AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD payment_method_id INT NOT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D5AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D5AA1164F ON payment (payment_method_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D5AA1164F');
        $this->addSql('ALTER TABLE cours_payment_method DROP FOREIGN KEY FK_BC1479C67ECF78B0');
        $this->addSql('ALTER TABLE cours_payment_method DROP FOREIGN KEY FK_BC1479C65AA1164F');
        $this->addSql('DROP TABLE cours_payment_method');
        $this->addSql('DROP TABLE payment_method');
        $this->addSql('DROP INDEX IDX_6D28840D5AA1164F ON payment');
        $this->addSql('ALTER TABLE payment DROP payment_method_id');
    }
}
