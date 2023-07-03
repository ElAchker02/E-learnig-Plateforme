<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606135647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE user ADD id_personne_id INT NOT NULL, DROP id_personne');
        // $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BA091CE5 FOREIGN KEY (id_personne_id) REFERENCES personne (id)');
        // $this->addSql('CREATE INDEX IDX_8D93D649BA091CE5 ON user (id_personne_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BA091CE5');
        $this->addSql('DROP INDEX IDX_8D93D649BA091CE5 ON user');
        $this->addSql('ALTER TABLE user ADD id_personne INT DEFAULT NULL, DROP id_personne_id');
    }
}
