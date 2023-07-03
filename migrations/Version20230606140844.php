<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606140844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE cours ADD id_enseignant_id INT NOT NULL');
        // $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C5A7D2DA5 FOREIGN KEY (id_enseignant_id) REFERENCES enseignant (id)');
        // $this->addSql('CREATE INDEX IDX_FDCA8C9C5A7D2DA5 ON cours (id_enseignant_id)');
        // $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BA091CE5 FOREIGN KEY (id_personne_id) REFERENCES personne (id)');
      
        // $this->addSql('CREATE INDEX IDX_8D93D649BA091CE5 ON user (id_personne_id)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C5A7D2DA5');
        $this->addSql('DROP INDEX IDX_FDCA8C9C5A7D2DA5 ON cours');
        $this->addSql('ALTER TABLE cours DROP id_enseignant_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BA091CE5');
        $this->addSql('DROP INDEX IDX_8D93D649BA091CE5 ON user');
    }
}
