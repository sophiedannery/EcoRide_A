<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529122550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON preference_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE preference_user ADD PRIMARY KEY (user_id, preference_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON preference_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE preference_user ADD PRIMARY KEY (preference_id, user_id)
        SQL);
    }
}
