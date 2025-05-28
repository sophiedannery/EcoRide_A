<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528115205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE suspension (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, admin_id INT NOT NULL, date_suspension DATE NOT NULL, motif LONGTEXT DEFAULT NULL, INDEX IDX_82AF0500A76ED395 (user_id), INDEX IDX_82AF0500642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suspension ADD CONSTRAINT FK_82AF0500A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suspension ADD CONSTRAINT FK_82AF0500642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE suspension DROP FOREIGN KEY FK_82AF0500A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suspension DROP FOREIGN KEY FK_82AF0500642B8210
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE suspension
        SQL);
    }
}
