<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230702210103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C1A65C546');
        $this->addSql('DROP INDEX IDX_FDCA8C9C1A65C546 ON cours');
        $this->addSql('ALTER TABLE cours DROP no_id');
        $this->addSql('ALTER TABLE partie ADD id_cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D2E149425 FOREIGN KEY (id_cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_59B1F3D2E149425 ON partie (id_cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD no_id INT NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C1A65C546 FOREIGN KEY (no_id) REFERENCES partie (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C1A65C546 ON cours (no_id)');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D2E149425');
        $this->addSql('DROP INDEX IDX_59B1F3D2E149425 ON partie');
        $this->addSql('ALTER TABLE partie DROP id_cours_id');
    }
}
