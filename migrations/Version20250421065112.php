<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421065112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE switch_salas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    ip VARCHAR(15) NOT NULL,
    CONSTRAINT uk_switch_salas_nombre UNIQUE (nombre),
    CONSTRAINT uk_switch_salas_ip UNIQUE (ip));");
//        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql(<<<'SQL'
//            CREATE UNIQUE INDEX UNIQ_AD8F32DD3A909126 ON switch_salas (nombre)
//        SQL);
//        $this->addSql(<<<'SQL'
//            CREATE UNIQUE INDEX UNIQ_AD8F32DDA5E3B32D ON switch_salas (ip)
//        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_AD8F32DD3A909126
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_AD8F32DDA5E3B32D
        SQL);
    }
}
