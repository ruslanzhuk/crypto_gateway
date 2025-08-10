<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250804172749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE telegram_bot_chat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE telegram_bot_integration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE telegram_bot_chat (id INT NOT NULL, integration_id INT DEFAULT NULL, chat_id INT NOT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B0BE6F909E82DDEA ON telegram_bot_chat (integration_id)');
        $this->addSql('CREATE TABLE telegram_bot_integration (id INT NOT NULL, creator_id UUID NOT NULL, bot_token VARCHAR(255) NOT NULL, bot_name VARCHAR(255) NOT NULL, bot_username VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C7E98E6561220EA6 ON telegram_bot_integration (creator_id)');
        $this->addSql('COMMENT ON COLUMN telegram_bot_integration.creator_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN telegram_bot_integration.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE telegram_bot_chat ADD CONSTRAINT FK_B0BE6F909E82DDEA FOREIGN KEY (integration_id) REFERENCES telegram_bot_integration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE telegram_bot_integration ADD CONSTRAINT FK_C7E98E6561220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP telegram_chat_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE telegram_bot_chat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE telegram_bot_integration_id_seq CASCADE');
        $this->addSql('ALTER TABLE telegram_bot_chat DROP CONSTRAINT FK_B0BE6F909E82DDEA');
        $this->addSql('ALTER TABLE telegram_bot_integration DROP CONSTRAINT FK_C7E98E6561220EA6');
        $this->addSql('DROP TABLE telegram_bot_chat');
        $this->addSql('DROP TABLE telegram_bot_integration');
        $this->addSql('ALTER TABLE "user" ADD telegram_chat_id VARCHAR(255) NOT NULL');
    }
}
