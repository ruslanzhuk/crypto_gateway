<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250705125626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE crypto_currency_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE fiat_currency_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE network_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payment_confirmation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payment_status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE crypto_currency (id INT NOT NULL, network_id_id INT DEFAULT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59320B70B15E270B ON crypto_currency (network_id_id)');
        $this->addSql('CREATE TABLE fiat_currency (id INT NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(30) NOT NULL, symbol VARCHAR(5) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE network (id INT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(100) NOT NULL, explorer_url TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE payment_confirmation (id INT NOT NULL, confirmed_by_id INT DEFAULT NULL, confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2ECDA5266F45385D ON payment_confirmation (confirmed_by_id)');
        $this->addSql('COMMENT ON COLUMN payment_confirmation.confirmed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE payment_status (id INT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE transaction (id INT NOT NULL, user_id_id INT DEFAULT NULL, wallet_id_id INT DEFAULT NULL, fiat_currency_id_id INT DEFAULT NULL, crypto_currency_id_id INT DEFAULT NULL, main_status_id INT DEFAULT NULL, manual_status_id INT DEFAULT NULL, automatic_status_id INT DEFAULT NULL, confirmation_id_id INT DEFAULT NULL, tx_hash TEXT NOT NULL, amount_fiat NUMERIC(10, 2) NOT NULL, amount_crypto NUMERIC(36, 18) NOT NULL, is_automatic BOOLEAN NOT NULL, received_amount_fiat NUMERIC(10, 2) NOT NULL, received_amount_crypto NUMERIC(36, 18) NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D19D86650F ON transaction (user_id_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D1F43F82D ON transaction (wallet_id_id)');
        $this->addSql('CREATE INDEX IDX_723705D1EF882977 ON transaction (fiat_currency_id_id)');
        $this->addSql('CREATE INDEX IDX_723705D1B0C16B0E ON transaction (crypto_currency_id_id)');
        $this->addSql('CREATE INDEX IDX_723705D14B41A71F ON transaction (main_status_id)');
        $this->addSql('CREATE INDEX IDX_723705D1EB30E2F0 ON transaction (manual_status_id)');
        $this->addSql('CREATE INDEX IDX_723705D187600571 ON transaction (automatic_status_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D1E02C8DFB ON transaction (confirmation_id_id)');
        $this->addSql('COMMENT ON COLUMN transaction.expired_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(120) NOT NULL, login VARCHAR(90) NOT NULL, password VARCHAR(255) NOT NULL, telegram_chat_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE wallet (id INT NOT NULL, user_id_id INT DEFAULT NULL, network_id_id INT DEFAULT NULL, public_adress VARCHAR(255) NOT NULL, private_key TEXT NOT NULL, seed_phrase TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7C68921F9D86650F ON wallet (user_id_id)');
        $this->addSql('CREATE INDEX IDX_7C68921FB15E270B ON wallet (network_id_id)');
        $this->addSql('COMMENT ON COLUMN wallet.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE crypto_currency ADD CONSTRAINT FK_59320B70B15E270B FOREIGN KEY (network_id_id) REFERENCES network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_confirmation ADD CONSTRAINT FK_2ECDA5266F45385D FOREIGN KEY (confirmed_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F43F82D FOREIGN KEY (wallet_id_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1EF882977 FOREIGN KEY (fiat_currency_id_id) REFERENCES fiat_currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1B0C16B0E FOREIGN KEY (crypto_currency_id_id) REFERENCES crypto_currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D14B41A71F FOREIGN KEY (main_status_id) REFERENCES payment_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1EB30E2F0 FOREIGN KEY (manual_status_id) REFERENCES payment_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D187600571 FOREIGN KEY (automatic_status_id) REFERENCES payment_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1E02C8DFB FOREIGN KEY (confirmation_id_id) REFERENCES payment_confirmation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921FB15E270B FOREIGN KEY (network_id_id) REFERENCES network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE crypto_currency_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE fiat_currency_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE network_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payment_confirmation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payment_status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE transaction_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE wallet_id_seq CASCADE');
        $this->addSql('ALTER TABLE crypto_currency DROP CONSTRAINT FK_59320B70B15E270B');
        $this->addSql('ALTER TABLE payment_confirmation DROP CONSTRAINT FK_2ECDA5266F45385D');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D19D86650F');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1F43F82D');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1EF882977');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1B0C16B0E');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D14B41A71F');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1EB30E2F0');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D187600571');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1E02C8DFB');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT FK_7C68921F9D86650F');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT FK_7C68921FB15E270B');
        $this->addSql('DROP TABLE crypto_currency');
        $this->addSql('DROP TABLE fiat_currency');
        $this->addSql('DROP TABLE network');
        $this->addSql('DROP TABLE payment_confirmation');
        $this->addSql('DROP TABLE payment_status');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
