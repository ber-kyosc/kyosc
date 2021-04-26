<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210318085959 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clan (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, INDEX IDX_9FF6A30C61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clan_user (clan_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D08640FBEAF84C8 (clan_id), INDEX IDX_D08640FA76ED395 (user_id), PRIMARY KEY(clan_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clan ADD CONSTRAINT FK_9FF6A30C61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE clan_user ADD CONSTRAINT FK_D08640FBEAF84C8 FOREIGN KEY (clan_id) REFERENCES clan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clan_user ADD CONSTRAINT FK_D08640FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clan_user DROP FOREIGN KEY FK_D08640FBEAF84C8');
        $this->addSql('DROP TABLE clan');
        $this->addSql('DROP TABLE clan_user');
    }
}
