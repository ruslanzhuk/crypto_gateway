<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250716150045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE fiat_amount_per_transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE fiat_amount_per_transaction (id INT NOT NULL, transaction_id INT DEFAULT NULL, fiat_currency_id INT DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, rate NUMERIC(36, 18) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F4E7CA522FC0CB0F ON fiat_amount_per_transaction (transaction_id)');
        $this->addSql('CREATE INDEX IDX_F4E7CA52C4F47010 ON fiat_amount_per_transaction (fiat_currency_id)');
        $this->addSql('COMMENT ON COLUMN fiat_amount_per_transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE fiat_amount_per_transaction ADD CONSTRAINT FK_F4E7CA522FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fiat_amount_per_transaction ADD CONSTRAINT FK_F4E7CA52C4F47010 FOREIGN KEY (fiat_currency_id) REFERENCES fiat_currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d1c4f47010');
        $this->addSql('DROP INDEX idx_723705d1c4f47010');
        $this->addSql('ALTER TABLE transaction DROP fiat_currency_id');
        $this->addSql('ALTER TABLE transaction DROP amount_fiat');
        $this->addSql('ALTER TABLE transaction DROP received_amount_fiat');
        $this->addSql('CREATE UNIQUE INDEX uniq_transaction_currency ON fiat_amount_per_transaction (transaction_id, fiat_currency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_transaction_currency');
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE fiat_amount_per_transaction_id_seq CASCADE');
        $this->addSql('ALTER TABLE fiat_amount_per_transaction DROP CONSTRAINT FK_F4E7CA522FC0CB0F');
        $this->addSql('ALTER TABLE fiat_amount_per_transaction DROP CONSTRAINT FK_F4E7CA52C4F47010');
        $this->addSql('DROP TABLE fiat_amount_per_transaction');
        $this->addSql('ALTER TABLE transaction ADD fiat_currency_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD amount_fiat NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD received_amount_fiat NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d1c4f47010 FOREIGN KEY (fiat_currency_id) REFERENCES fiat_currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_723705d1c4f47010 ON transaction (fiat_currency_id)');
    }
}
