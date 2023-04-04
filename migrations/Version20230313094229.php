<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230313094229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FCEC9EFBA14FCCC ON personne (invitation_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FCEC9EFFBAD55A5 ON personne (invitation_link)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_FCEC9EFBA14FCCC ON personne');
        $this->addSql('DROP INDEX UNIQ_FCEC9EFFBAD55A5 ON personne');
    }
}
