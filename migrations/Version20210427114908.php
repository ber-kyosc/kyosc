<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210427114908 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clan_challenge (clan_id INT NOT NULL, challenge_id INT NOT NULL, INDEX IDX_7D046DF3BEAF84C8 (clan_id), INDEX IDX_7D046DF398A21AC6 (challenge_id), PRIMARY KEY(clan_id, challenge_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clan_challenge ADD CONSTRAINT FK_7D046DF3BEAF84C8 FOREIGN KEY (clan_id) REFERENCES clan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clan_challenge ADD CONSTRAINT FK_7D046DF398A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE clan_challenge');
    }
}
