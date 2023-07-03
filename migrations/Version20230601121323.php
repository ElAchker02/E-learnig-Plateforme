<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601121323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etudiant_cours_payant (id INT AUTO_INCREMENT NOT NULL, id_etudiant INT NOT NULL, id_cours INT NOT NULL, date_achat DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
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
        $this->addSql('DROP TABLE etudiant_cours_payant');
        $this->addSql('ALTER TABLE chapitre CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE cours CHANGE introduction introduction TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE partie CHANGE description description LONGTEXT DEFAULT NULL');
    }
}
