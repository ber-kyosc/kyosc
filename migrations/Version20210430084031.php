<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210430084031 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, clan_id INT DEFAULT NULL, challenge_id INT DEFAULT NULL, link VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_7CC7DA2CF675F31B (author_id), INDEX IDX_7CC7DA2CBEAF84C8 (clan_id), INDEX IDX_7CC7DA2C98A21AC6 (challenge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CBEAF84C8 FOREIGN KEY (clan_id) REFERENCES clan (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE video');
    }
}
