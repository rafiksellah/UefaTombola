<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240108153030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_3BAE0AA7E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, user INT NOT NULL, nulber INT NOT NULL, place VARCHAR(255) NOT NULL, last_win DATETIME NOT NULL, created_at DATETIME NOT NULL, update_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift_quantity (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, initial_quantity INT NOT NULL, quantity_left INT NOT NULL, INDEX IDX_D9F6F27CE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE gift_quantity ADD CONSTRAINT FK_D9F6F27CE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7E48FD905');
        $this->addSql('ALTER TABLE gift_quantity DROP FOREIGN KEY FK_D9F6F27CE48FD905');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE gift_quantity');
    }
}
