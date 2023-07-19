<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719022216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0C5A7D2DA5 FOREIGN KEY (id_enseignant_id) REFERENCES enseignant (id)');
        // $this->addSql('CREATE INDEX IDX_D87F7E0C5A7D2DA5 ON test (id_enseignant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0C5A7D2DA5');
        $this->addSql('DROP INDEX IDX_D87F7E0C5A7D2DA5 ON test');
    }
}
