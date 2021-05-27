<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210526210700 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE join_request ADD requested_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE join_request ADD CONSTRAINT FK_E932E4FF65A2CAD1 FOREIGN KEY (requested_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E932E4FF65A2CAD1 ON join_request (requested_user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE join_request DROP FOREIGN KEY FK_E932E4FF65A2CAD1');
        $this->addSql('DROP INDEX IDX_E932E4FF65A2CAD1 ON join_request');
        $this->addSql('ALTER TABLE join_request DROP requested_user_id');
    }
}
