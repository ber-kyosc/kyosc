<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210105150549 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge ADD location_end VARCHAR(50) DEFAULT NULL, ADD journey LONGTEXT NOT NULL, ADD distance DOUBLE PRECISION DEFAULT NULL, ADD information LONGTEXT NOT NULL, ADD is_public TINYINT(1) NOT NULL, DROP date_end');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge ADD date_end DATE NOT NULL, DROP location_end, DROP journey, DROP distance, DROP information, DROP is_public');
    }
}
