<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606132628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre CHANGE id_cours id_cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B0252E149425 FOREIGN KEY (id_cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_8C62B0252E149425 ON chapitre (id_cours_id)');
        $this->addSql('ALTER TABLE cours CHANGE id_categorie id_categorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C9F34925F FOREIGN KEY (id_categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C9F34925F ON cours (id_categorie_id)');
        $this->addSql('ALTER TABLE devoir ADD id_cours_id INT NOT NULL, ADD id_enseignant_id INT NOT NULL, DROP id_cours, DROP id_enseignant');
        $this->addSql('ALTER TABLE devoir ADD CONSTRAINT FK_749EA7712E149425 FOREIGN KEY (id_cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE devoir ADD CONSTRAINT FK_749EA7715A7D2DA5 FOREIGN KEY (id_enseignant_id) REFERENCES enseignant (id)');
        $this->addSql('CREATE INDEX IDX_749EA7712E149425 ON devoir (id_cours_id)');
        $this->addSql('CREATE INDEX IDX_749EA7715A7D2DA5 ON devoir (id_enseignant_id)');
        $this->addSql('ALTER TABLE devoir_soummit ADD id_etudiant_id INT NOT NULL, ADD id_devoir_id INT NOT NULL, DROP id_etudiant, DROP id_devoir');
        $this->addSql('ALTER TABLE devoir_soummit ADD CONSTRAINT FK_5FE87931C5F87C54 FOREIGN KEY (id_etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE devoir_soummit ADD CONSTRAINT FK_5FE8793145B6EF69 FOREIGN KEY (id_devoir_id) REFERENCES devoir (id)');
        $this->addSql('CREATE INDEX IDX_5FE87931C5F87C54 ON devoir_soummit (id_etudiant_id)');
        $this->addSql('CREATE INDEX IDX_5FE8793145B6EF69 ON devoir_soummit (id_devoir_id)');
        $this->addSql('ALTER TABLE enseignant CHANGE id_personne id_personne_id INT NOT NULL');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1BA091CE5 FOREIGN KEY (id_personne_id) REFERENCES personne (id)');
        $this->addSql('CREATE INDEX IDX_81A72FA1BA091CE5 ON enseignant (id_personne_id)');
        $this->addSql('ALTER TABLE etudiant_cours_payant ADD id_etudiant_id INT NOT NULL, ADD id_cours_id INT NOT NULL, DROP id_etudiant, DROP id_cours');
        $this->addSql('ALTER TABLE etudiant_cours_payant ADD CONSTRAINT FK_8ADB7DE0C5F87C54 FOREIGN KEY (id_etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE etudiant_cours_payant ADD CONSTRAINT FK_8ADB7DE02E149425 FOREIGN KEY (id_cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_8ADB7DE0C5F87C54 ON etudiant_cours_payant (id_etudiant_id)');
        $this->addSql('CREATE INDEX IDX_8ADB7DE02E149425 ON etudiant_cours_payant (id_cours_id)');
        $this->addSql('ALTER TABLE note_test ADD id_test_id INT NOT NULL, ADD id_etudiant_id INT NOT NULL, DROP id_etudiant, DROP id_test');
        $this->addSql('ALTER TABLE note_test ADD CONSTRAINT FK_78056F54C0C0AD29 FOREIGN KEY (id_test_id) REFERENCES test (id)');
        $this->addSql('ALTER TABLE note_test ADD CONSTRAINT FK_78056F54C5F87C54 FOREIGN KEY (id_etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('CREATE INDEX IDX_78056F54C0C0AD29 ON note_test (id_test_id)');
        $this->addSql('CREATE INDEX IDX_78056F54C5F87C54 ON note_test (id_etudiant_id)');
        $this->addSql('ALTER TABLE partie CHANGE id_chapitre id_chapitre_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D7AC228C FOREIGN KEY (id_chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('CREATE INDEX IDX_59B1F3D7AC228C ON partie (id_chapitre_id)');
        $this->addSql('ALTER TABLE progression ADD id_etudiant_id INT NOT NULL, ADD id_cours_id INT NOT NULL, DROP id_etudiant, DROP id_cours');
        $this->addSql('ALTER TABLE progression ADD CONSTRAINT FK_D5B25073C5F87C54 FOREIGN KEY (id_etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE progression ADD CONSTRAINT FK_D5B250732E149425 FOREIGN KEY (id_cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_D5B25073C5F87C54 ON progression (id_etudiant_id)');
        $this->addSql('CREATE INDEX IDX_D5B250732E149425 ON progression (id_cours_id)');
        $this->addSql('ALTER TABLE question ADD id_test_id INT NOT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EC0C0AD29 FOREIGN KEY (id_test_id) REFERENCES test (id)');
        $this->addSql('CREATE INDEX IDX_B6F7494EC0C0AD29 ON question (id_test_id)');
        $this->addSql('ALTER TABLE reponse ADD id_question_id INT NOT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC76353B48 FOREIGN KEY (id_question_id) REFERENCES question (id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC76353B48 ON reponse (id_question_id)');
        $this->addSql('ALTER TABLE test ADD id_cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0C2E149425 FOREIGN KEY (id_cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_D87F7E0C2E149425 ON test (id_cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B0252E149425');
        $this->addSql('DROP INDEX IDX_8C62B0252E149425 ON chapitre');
        $this->addSql('ALTER TABLE chapitre CHANGE id_cours_id id_cours INT NOT NULL');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C9F34925F');
        $this->addSql('DROP INDEX IDX_FDCA8C9C9F34925F ON cours');
        $this->addSql('ALTER TABLE cours CHANGE id_categorie_id id_categorie INT NOT NULL');
        $this->addSql('ALTER TABLE devoir DROP FOREIGN KEY FK_749EA7712E149425');
        $this->addSql('ALTER TABLE devoir DROP FOREIGN KEY FK_749EA7715A7D2DA5');
        $this->addSql('DROP INDEX IDX_749EA7712E149425 ON devoir');
        $this->addSql('DROP INDEX IDX_749EA7715A7D2DA5 ON devoir');
        $this->addSql('ALTER TABLE devoir ADD id_cours INT NOT NULL, ADD id_enseignant INT NOT NULL, DROP id_cours_id, DROP id_enseignant_id');
        $this->addSql('ALTER TABLE devoir_soummit DROP FOREIGN KEY FK_5FE87931C5F87C54');
        $this->addSql('ALTER TABLE devoir_soummit DROP FOREIGN KEY FK_5FE8793145B6EF69');
        $this->addSql('DROP INDEX IDX_5FE87931C5F87C54 ON devoir_soummit');
        $this->addSql('DROP INDEX IDX_5FE8793145B6EF69 ON devoir_soummit');
        $this->addSql('ALTER TABLE devoir_soummit ADD id_etudiant INT NOT NULL, ADD id_devoir INT NOT NULL, DROP id_etudiant_id, DROP id_devoir_id');
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1BA091CE5');
        $this->addSql('DROP INDEX IDX_81A72FA1BA091CE5 ON enseignant');
        $this->addSql('ALTER TABLE enseignant CHANGE id_personne_id id_personne INT NOT NULL');
        $this->addSql('ALTER TABLE etudiant_cours_payant DROP FOREIGN KEY FK_8ADB7DE0C5F87C54');
        $this->addSql('ALTER TABLE etudiant_cours_payant DROP FOREIGN KEY FK_8ADB7DE02E149425');
        $this->addSql('DROP INDEX IDX_8ADB7DE0C5F87C54 ON etudiant_cours_payant');
        $this->addSql('DROP INDEX IDX_8ADB7DE02E149425 ON etudiant_cours_payant');
        $this->addSql('ALTER TABLE etudiant_cours_payant ADD id_etudiant INT NOT NULL, ADD id_cours INT NOT NULL, DROP id_etudiant_id, DROP id_cours_id');
        $this->addSql('ALTER TABLE note_test DROP FOREIGN KEY FK_78056F54C0C0AD29');
        $this->addSql('ALTER TABLE note_test DROP FOREIGN KEY FK_78056F54C5F87C54');
        $this->addSql('DROP INDEX IDX_78056F54C0C0AD29 ON note_test');
        $this->addSql('DROP INDEX IDX_78056F54C5F87C54 ON note_test');
        $this->addSql('ALTER TABLE note_test ADD id_etudiant INT NOT NULL, ADD id_test INT NOT NULL, DROP id_test_id, DROP id_etudiant_id');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D7AC228C');
        $this->addSql('DROP INDEX IDX_59B1F3D7AC228C ON partie');
        $this->addSql('ALTER TABLE partie CHANGE id_chapitre_id id_chapitre INT NOT NULL');
        $this->addSql('ALTER TABLE progression DROP FOREIGN KEY FK_D5B25073C5F87C54');
        $this->addSql('ALTER TABLE progression DROP FOREIGN KEY FK_D5B250732E149425');
        $this->addSql('DROP INDEX IDX_D5B25073C5F87C54 ON progression');
        $this->addSql('DROP INDEX IDX_D5B250732E149425 ON progression');
        $this->addSql('ALTER TABLE progression ADD id_etudiant INT NOT NULL, ADD id_cours INT NOT NULL, DROP id_etudiant_id, DROP id_cours_id');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EC0C0AD29');
        $this->addSql('DROP INDEX IDX_B6F7494EC0C0AD29 ON question');
        $this->addSql('ALTER TABLE question DROP id_test_id');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC76353B48');
        $this->addSql('DROP INDEX IDX_5FB6DEC76353B48 ON reponse');
        $this->addSql('ALTER TABLE reponse DROP id_question_id');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0C2E149425');
        $this->addSql('DROP INDEX IDX_D87F7E0C2E149425 ON test');
        $this->addSql('ALTER TABLE test DROP id_cours_id');
    }
}
