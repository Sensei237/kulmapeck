<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409221728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement_payment_method (abonnement_id INT NOT NULL, payment_method_id INT NOT NULL, INDEX IDX_1C99C877F1D74413 (abonnement_id), INDEX IDX_1C99C8775AA1164F (payment_method_id), PRIMARY KEY(abonnement_id, payment_method_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abonnement_payment_method ADD CONSTRAINT FK_1C99C877F1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abonnement_payment_method ADD CONSTRAINT FK_1C99C8775AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement_payment_method DROP FOREIGN KEY FK_1C99C877F1D74413');
        $this->addSql('ALTER TABLE abonnement_payment_method DROP FOREIGN KEY FK_1C99C8775AA1164F');
        $this->addSql('DROP TABLE abonnement_payment_method');
    }
}
