<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230715113451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE chapitre DROP ord');
        // $this->addSql('CREATE TRIGGER nbChapitres 
        // AFTER INSERT ON chapitre 
        // FOR EACH ROW 
        // BEGIN 
        //     UPDATE cours set nb_chapitres = nb_chapitres + 1;
        // END;');
        // $this->addSql('CREATE TRIGGER nbChapitres 
        // AFTER DELETE ON chapitre 
        // FOR EACH ROW 
        // BEGIN 
        //     UPDATE cours set nb_chapitres = nb_chapitres - 1;
        // END;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre ADD ord INT DEFAULT NULL');
    }
}
