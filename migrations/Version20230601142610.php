<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601142610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devoir (id INT AUTO_INCREMENT NOT NULL, nom_devoir VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_depot DATE NOT NULL, date_soumission DATE NOT NULL, images JSON DEFAULT NULL, fichier VARCHAR(255) DEFAULT NULL, id_cours INT NOT NULL, id_enseignant INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE devoir_soummit (id INT AUTO_INCREMENT NOT NULL, id_etudiant INT NOT NULL, id_devoir INT NOT NULL, devoir_soummit VARCHAR(255) NOT NULL, note DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note_test (id INT AUTO_INCREMENT NOT NULL, id_etudiant INT NOT NULL, id_test INT NOT NULL, note DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, valide TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, nom_test VARCHAR(255) NOT NULL, duree INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapitre CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cours CHANGE introduction introduction VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE partie CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE cours MODIFY introduction TEXT');
        $this->addSql('ALTER TABLE chapitre MODIFY description TEXT');
        $this->addSql('ALTER TABLE partie MODIFY description TEXT');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE devoir');
        $this->addSql('DROP TABLE devoir_soummit');
        $this->addSql('DROP TABLE note_test');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE test');
        $this->addSql('ALTER TABLE chapitre CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours CHANGE introduction introduction TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE partie CHANGE description description TEXT DEFAULT NULL');
    }
}
