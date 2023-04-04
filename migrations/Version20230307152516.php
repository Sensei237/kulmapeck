<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307152516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapitre (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, objectifs LONGTEXT NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_8C62B0257ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, specialite_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_8F87BF962195E0F0 (specialite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, enseignant_id INT NOT NULL, intitule VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, objectifs LONGTEXT NOT NULL, description LONGTEXT NOT NULL, is_published TINYINT(1) NOT NULL, is_free TINYINT(1) NOT NULL, niveau_difficulte VARCHAR(255) NOT NULL, duree_apprentissage VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', vues INT NOT NULL, is_validated TINYINT(1) NOT NULL, INDEX IDX_FDCA8C9CE455FCC0 (enseignant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours_classe (cours_id INT NOT NULL, classe_id INT NOT NULL, INDEX IDX_E007AEFE7ECF78B0 (cours_id), INDEX IDX_E007AEFE8F5EA509 (classe_id), PRIMARY KEY(cours_id, classe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, classe_id INT DEFAULT NULL, utilisateur_id INT NOT NULL, etablissement_id INT DEFAULT NULL, reference VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_ECA105F7AEA34913 (reference), INDEX IDX_ECA105F78F5EA509 (classe_id), UNIQUE INDEX UNIQ_ECA105F7FB88E14F (utilisateur_id), INDEX IDX_ECA105F7FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve_cours (eleve_id INT NOT NULL, cours_id INT NOT NULL, INDEX IDX_E2AA9175A6CC7B2 (eleve_id), INDEX IDX_E2AA91757ECF78B0 (cours_id), PRIMARY KEY(eleve_id, cours_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enseignant (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, etablissement_id INT DEFAULT NULL, reference VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_81A72FA1AEA34913 (reference), UNIQUE INDEX UNIQ_81A72FA1FB88E14F (utilisateur_id), INDEX IDX_81A72FA1FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etablissement (id INT AUTO_INCREMENT NOT NULL, pays_id INT NOT NULL, name VARCHAR(255) NOT NULL, ville VARCHAR(150) NOT NULL, INDEX IDX_20FD592CA6E44244 (pays_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filiere (id INT AUTO_INCREMENT NOT NULL, type_enseignement_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_2ED05D9E5CD8AF54 (type_enseignement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filiere_sous_systeme (filiere_id INT NOT NULL, sous_systeme_id INT NOT NULL, INDEX IDX_FEBF7840180AA129 (filiere_id), INDEX IDX_FEBF7840D21722C4 (sous_systeme_id), PRIMARY KEY(filiere_id, sous_systeme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, duree VARCHAR(255) NOT NULL, niveau_difficulte VARCHAR(255) NOT NULL, is_free TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_published TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation_cours (formation_id INT NOT NULL, cours_id INT NOT NULL, INDEX IDX_8B4112E95200282E (formation_id), INDEX IDX_8B4112E97ECF78B0 (cours_id), PRIMARY KEY(formation_id, cours_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation_eleve (formation_id INT NOT NULL, eleve_id INT NOT NULL, INDEX IDX_9A2A9B825200282E (formation_id), INDEX IDX_9A2A9B82A6CC7B2 (eleve_id), PRIMARY KEY(formation_id, eleve_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, UNIQUE INDEX UNIQ_852BBECD7ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum_message (id INT AUTO_INCREMENT NOT NULL, membre_id INT NOT NULL, sujet_id INT NOT NULL, crated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content LONGTEXT NOT NULL, likes INT DEFAULT NULL, INDEX IDX_47717D0E6A99F74A (membre_id), INDEX IDX_47717D0E7C4D497E (sujet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `kulmapeck_cours_like` (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, cours_id INT NOT NULL, is_like TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CAA5FE82F675F31B (author_id), INDEX IDX_CAA5FE827ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `kulmapeck_user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9BE902C8E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, chapitre_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, objectifs LONGTEXT NOT NULL, content LONGTEXT NOT NULL, type SMALLINT NOT NULL, INDEX IDX_F87474F31FBEEF7B (chapitre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membre (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_F6B4FB29FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membre_forum (membre_id INT NOT NULL, forum_id INT NOT NULL, INDEX IDX_330DB14C6A99F74A (membre_id), INDEX IDX_330DB14C29CCBAD0 (forum_id), PRIMARY KEY(membre_id, forum_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, destinataire_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, INDEX IDX_BF5476CAA4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pays (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, code VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, parent_id INT DEFAULT NULL, pays_id INT DEFAULT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, born_at DATE NOT NULL, lieu_naissance VARCHAR(255) NOT NULL, sexe VARCHAR(100) NOT NULL, avatar VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) NOT NULL, invitation_code VARCHAR(255) NOT NULL, invitation_link VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FCEC9EFFB88E14F (utilisateur_id), INDEX IDX_FCEC9EF727ACA70 (parent_id), INDEX IDX_FCEC9EFA6E44244 (pays_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposition (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, content LONGTEXT NOT NULL, is_true TINYINT(1) NOT NULL, INDEX IDX_C7CDC353853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, chapitre_id INT NOT NULL, question LONGTEXT NOT NULL, reference VARCHAR(255) NOT NULL, INDEX IDX_A412FA921FBEEF7B (chapitre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, proposition_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5FB6DEC7A6CC7B2 (eleve_id), INDEX IDX_5FB6DEC7DB96F9E (proposition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_systeme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialite (id INT AUTO_INCREMENT NOT NULL, filiere_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_E7D6FCC1180AA129 (filiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sujet (id INT AUTO_INCREMENT NOT NULL, membre_id INT NOT NULL, forum_id INT NOT NULL, content LONGTEXT NOT NULL, is_solved TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2E13599D6A99F74A (membre_id), INDEX IDX_2E13599D29CCBAD0 (forum_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_enseignement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B0257ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF962195E0F0 FOREIGN KEY (specialite_id) REFERENCES specialite (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CE455FCC0 FOREIGN KEY (enseignant_id) REFERENCES enseignant (id)');
        $this->addSql('ALTER TABLE cours_classe ADD CONSTRAINT FK_E007AEFE7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_classe ADD CONSTRAINT FK_E007AEFE8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F78F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F7FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE eleve_cours ADD CONSTRAINT FK_E2AA9175A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE eleve_cours ADD CONSTRAINT FK_E2AA91757ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592CA6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE filiere ADD CONSTRAINT FK_2ED05D9E5CD8AF54 FOREIGN KEY (type_enseignement_id) REFERENCES type_enseignement (id)');
        $this->addSql('ALTER TABLE filiere_sous_systeme ADD CONSTRAINT FK_FEBF7840180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE filiere_sous_systeme ADD CONSTRAINT FK_FEBF7840D21722C4 FOREIGN KEY (sous_systeme_id) REFERENCES sous_systeme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_cours ADD CONSTRAINT FK_8B4112E95200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_cours ADD CONSTRAINT FK_8B4112E97ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_eleve ADD CONSTRAINT FK_9A2A9B825200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_eleve ADD CONSTRAINT FK_9A2A9B82A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum ADD CONSTRAINT FK_852BBECD7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE forum_message ADD CONSTRAINT FK_47717D0E6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE forum_message ADD CONSTRAINT FK_47717D0E7C4D497E FOREIGN KEY (sujet_id) REFERENCES sujet (id)');
        $this->addSql('ALTER TABLE `kulmapeck_cours_like` ADD CONSTRAINT FK_CAA5FE82F675F31B FOREIGN KEY (author_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('ALTER TABLE `kulmapeck_cours_like` ADD CONSTRAINT FK_CAA5FE827ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F31FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT FK_F6B4FB29FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('ALTER TABLE membre_forum ADD CONSTRAINT FK_330DB14C6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membre_forum ADD CONSTRAINT FK_330DB14C29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `kulmapeck_user` (id)');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EF727ACA70 FOREIGN KEY (parent_id) REFERENCES personne (id)');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFA6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC353853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7DB96F9E FOREIGN KEY (proposition_id) REFERENCES proposition (id)');
        $this->addSql('ALTER TABLE specialite ADD CONSTRAINT FK_E7D6FCC1180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE sujet ADD CONSTRAINT FK_2E13599D6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE sujet ADD CONSTRAINT FK_2E13599D29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B0257ECF78B0');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF962195E0F0');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CE455FCC0');
        $this->addSql('ALTER TABLE cours_classe DROP FOREIGN KEY FK_E007AEFE7ECF78B0');
        $this->addSql('ALTER TABLE cours_classe DROP FOREIGN KEY FK_E007AEFE8F5EA509');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F78F5EA509');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7FB88E14F');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7FF631228');
        $this->addSql('ALTER TABLE eleve_cours DROP FOREIGN KEY FK_E2AA9175A6CC7B2');
        $this->addSql('ALTER TABLE eleve_cours DROP FOREIGN KEY FK_E2AA91757ECF78B0');
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1FB88E14F');
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1FF631228');
        $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592CA6E44244');
        $this->addSql('ALTER TABLE filiere DROP FOREIGN KEY FK_2ED05D9E5CD8AF54');
        $this->addSql('ALTER TABLE filiere_sous_systeme DROP FOREIGN KEY FK_FEBF7840180AA129');
        $this->addSql('ALTER TABLE filiere_sous_systeme DROP FOREIGN KEY FK_FEBF7840D21722C4');
        $this->addSql('ALTER TABLE formation_cours DROP FOREIGN KEY FK_8B4112E95200282E');
        $this->addSql('ALTER TABLE formation_cours DROP FOREIGN KEY FK_8B4112E97ECF78B0');
        $this->addSql('ALTER TABLE formation_eleve DROP FOREIGN KEY FK_9A2A9B825200282E');
        $this->addSql('ALTER TABLE formation_eleve DROP FOREIGN KEY FK_9A2A9B82A6CC7B2');
        $this->addSql('ALTER TABLE forum DROP FOREIGN KEY FK_852BBECD7ECF78B0');
        $this->addSql('ALTER TABLE forum_message DROP FOREIGN KEY FK_47717D0E6A99F74A');
        $this->addSql('ALTER TABLE forum_message DROP FOREIGN KEY FK_47717D0E7C4D497E');
        $this->addSql('ALTER TABLE `kulmapeck_cours_like` DROP FOREIGN KEY FK_CAA5FE82F675F31B');
        $this->addSql('ALTER TABLE `kulmapeck_cours_like` DROP FOREIGN KEY FK_CAA5FE827ECF78B0');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F31FBEEF7B');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB29FB88E14F');
        $this->addSql('ALTER TABLE membre_forum DROP FOREIGN KEY FK_330DB14C6A99F74A');
        $this->addSql('ALTER TABLE membre_forum DROP FOREIGN KEY FK_330DB14C29CCBAD0');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA4F84F6E');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFFB88E14F');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EF727ACA70');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFA6E44244');
        $this->addSql('ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC353853CD175');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA921FBEEF7B');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7A6CC7B2');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7DB96F9E');
        $this->addSql('ALTER TABLE specialite DROP FOREIGN KEY FK_E7D6FCC1180AA129');
        $this->addSql('ALTER TABLE sujet DROP FOREIGN KEY FK_2E13599D6A99F74A');
        $this->addSql('ALTER TABLE sujet DROP FOREIGN KEY FK_2E13599D29CCBAD0');
        $this->addSql('DROP TABLE chapitre');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE cours_classe');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE eleve_cours');
        $this->addSql('DROP TABLE enseignant');
        $this->addSql('DROP TABLE etablissement');
        $this->addSql('DROP TABLE filiere');
        $this->addSql('DROP TABLE filiere_sous_systeme');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE formation_cours');
        $this->addSql('DROP TABLE formation_eleve');
        $this->addSql('DROP TABLE forum');
        $this->addSql('DROP TABLE forum_message');
        $this->addSql('DROP TABLE `kulmapeck_cours_like`');
        $this->addSql('DROP TABLE `kulmapeck_user`');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE membre');
        $this->addSql('DROP TABLE membre_forum');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE pays');
        $this->addSql('DROP TABLE personne');
        $this->addSql('DROP TABLE proposition');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE sous_systeme');
        $this->addSql('DROP TABLE specialite');
        $this->addSql('DROP TABLE sujet');
        $this->addSql('DROP TABLE type_enseignement');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
