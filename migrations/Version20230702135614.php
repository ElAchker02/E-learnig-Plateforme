<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230702135614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CE075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
        // $this->addSql('CREATE INDEX IDX_FDCA8C9CE075F7A4 ON cours (partie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CE075F7A4');
        $this->addSql('DROP INDEX IDX_FDCA8C9CE075F7A4 ON cours');
    }
}
