<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505085810 extends AbstractMigration {

    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE vlan_consejeria_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE vlan_consejeria (id INT NOT NULL, vlanid VARCHAR(255) DEFAULT '0' NOT NULL, consejeria VARCHAR(255) DEFAULT 'Hacienda' NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX unique_vlan_consejeria ON vlan_consejeria (vlanid, consejeria)
        SQL);
    }

    public function down(Schema $schema): void {

    }
}
