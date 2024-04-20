<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240420141349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, user_name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, consent TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D79F6B11E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE event CHANGE message text LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE game ADD tombola_winner_id INT DEFAULT NULL, ADD city_name VARCHAR(100) NOT NULL, ADD city_code INT NOT NULL, ADD type INT NOT NULL, DROP place, DROP last_win');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CDC10F41E FOREIGN KEY (tombola_winner_id) REFERENCES participant (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318CDC10F41E ON game (tombola_winner_id)');
        $this->addSql('ALTER TABLE gift_quantity CHANGE quantity_left quantity_used INT NOT NULL, CHANGE gift_label name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CDC10F41E');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11E48FD905');
        $this->addSql('DROP TABLE participant');
        $this->addSql('ALTER TABLE event CHANGE text message LONGTEXT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_232B318CDC10F41E ON game');
        $this->addSql('ALTER TABLE game ADD place VARCHAR(255) NOT NULL, ADD last_win DATETIME NOT NULL, DROP tombola_winner_id, DROP city_name, DROP city_code, DROP type');
        $this->addSql('ALTER TABLE gift_quantity CHANGE quantity_used quantity_left INT NOT NULL, CHANGE name gift_label VARCHAR(255) DEFAULT NULL');
    }
}
