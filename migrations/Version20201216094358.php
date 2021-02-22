<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201216094358 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, quotation VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, location VARCHAR(50) NOT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge_sport (challenge_id INT NOT NULL, sport_id INT NOT NULL, INDEX IDX_1E5766C098A21AC6 (challenge_id), INDEX IDX_1E5766C0AC78BCF8 (sport_id), PRIMARY KEY(challenge_id, sport_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, logo VARCHAR(100) NOT NULL, color VARCHAR(7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE challenge_sport ADD CONSTRAINT FK_1E5766C098A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE challenge_sport ADD CONSTRAINT FK_1E5766C0AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_sport DROP FOREIGN KEY FK_1E5766C098A21AC6');
        $this->addSql('ALTER TABLE challenge_sport DROP FOREIGN KEY FK_1E5766C0AC78BCF8');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE challenge_sport');
        $this->addSql('DROP TABLE sport');
    }
}
