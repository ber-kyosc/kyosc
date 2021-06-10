<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610113246 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, challenge_id INT DEFAULT NULL, clan_id INT DEFAULT NULL, author_id INT NOT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_16DB4F8998A21AC6 (challenge_id), INDEX IDX_16DB4F89BEAF84C8 (clan_id), INDEX IDX_16DB4F89F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8998A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89BEAF84C8 FOREIGN KEY (clan_id) REFERENCES clan (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE picture');
    }
}
