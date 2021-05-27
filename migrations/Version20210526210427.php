<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210526210427 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE join_request (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, challenge_id INT DEFAULT NULL, clan_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_accepted TINYINT(1) NOT NULL, is_rejected TINYINT(1) NOT NULL, INDEX IDX_E932E4FF61220EA6 (creator_id), INDEX IDX_E932E4FF98A21AC6 (challenge_id), INDEX IDX_E932E4FFBEAF84C8 (clan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE join_request ADD CONSTRAINT FK_E932E4FF61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE join_request ADD CONSTRAINT FK_E932E4FF98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        $this->addSql('ALTER TABLE join_request ADD CONSTRAINT FK_E932E4FFBEAF84C8 FOREIGN KEY (clan_id) REFERENCES clan (id)');
        $this->addSql('DROP TABLE request');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, challenge_id INT DEFAULT NULL, clan_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_accepted TINYINT(1) NOT NULL, is_rejected TINYINT(1) NOT NULL, INDEX IDX_3B978F9F61220EA6 (creator_id), INDEX IDX_3B978F9F98A21AC6 (challenge_id), INDEX IDX_3B978F9FBEAF84C8 (clan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FBEAF84C8 FOREIGN KEY (clan_id) REFERENCES clan (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE join_request');
    }
}
