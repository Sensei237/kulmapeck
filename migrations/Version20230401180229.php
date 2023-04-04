<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230401180229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecture ADD chapitre_id INT DEFAULT NULL, ADD cours_id INT DEFAULT NULL, CHANGE lesson_id lesson_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lecture ADD CONSTRAINT FK_C16779481FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('ALTER TABLE lecture ADD CONSTRAINT FK_C16779487ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_C16779481FBEEF7B ON lecture (chapitre_id)');
        $this->addSql('CREATE INDEX IDX_C16779487ECF78B0 ON lecture (cours_id)');
        $this->addSql('ALTER TABLE quiz ADD cours_id INT DEFAULT NULL, CHANGE chapitre_id chapitre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA927ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_A412FA927ECF78B0 ON quiz (cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecture DROP FOREIGN KEY FK_C16779481FBEEF7B');
        $this->addSql('ALTER TABLE lecture DROP FOREIGN KEY FK_C16779487ECF78B0');
        $this->addSql('DROP INDEX IDX_C16779481FBEEF7B ON lecture');
        $this->addSql('DROP INDEX IDX_C16779487ECF78B0 ON lecture');
        $this->addSql('ALTER TABLE lecture DROP chapitre_id, DROP cours_id, CHANGE lesson_id lesson_id INT NOT NULL');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA927ECF78B0');
        $this->addSql('DROP INDEX IDX_A412FA927ECF78B0 ON quiz');
        $this->addSql('ALTER TABLE quiz DROP cours_id, CHANGE chapitre_id chapitre_id INT NOT NULL');
    }
}
