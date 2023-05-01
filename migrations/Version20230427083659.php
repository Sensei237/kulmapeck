<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230427083659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_template ADD CONSTRAINT FK_C2702726C54C8C93 FOREIGN KEY (type_id) REFERENCES notification_type (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2702726C54C8C93 ON notification_template (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_template DROP FOREIGN KEY FK_C2702726C54C8C93');
        $this->addSql('DROP INDEX UNIQ_C2702726C54C8C93 ON notification_template');
    }
}
