<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512090738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE switch_salas_id_seq INCREMENT BY 1 MINVALUE 1 START 11
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE switch_salas ALTER COLUMN id SET DEFAULT nextval('switch_salas_id_seq')
        SQL);
    }

    public function down(Schema $schema): void
    {
       
    }
}
