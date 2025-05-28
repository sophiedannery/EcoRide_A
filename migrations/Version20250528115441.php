<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528115441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE preference (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE preference_user (preference_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FA648E65D81022C0 (preference_id), INDEX IDX_FA648E65A76ED395 (user_id), PRIMARY KEY(preference_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE preference_user ADD CONSTRAINT FK_FA648E65D81022C0 FOREIGN KEY (preference_id) REFERENCES preference (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE preference_user ADD CONSTRAINT FK_FA648E65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE preference_user DROP FOREIGN KEY FK_FA648E65D81022C0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE preference_user DROP FOREIGN KEY FK_FA648E65A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE preference
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE preference_user
        SQL);
    }
}
