<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706103106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d19d86650f');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d1b0c16b0e');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d1e02c8dfb');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d1ef882977');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d1f43f82d');
        $this->addSql('DROP INDEX idx_723705d19d86650f');
        $this->addSql('DROP INDEX idx_723705d1b0c16b0e');
        $this->addSql('DROP INDEX idx_723705d1ef882977');
        $this->addSql('DROP INDEX uniq_723705d1e02c8dfb');
        $this->addSql('DROP INDEX uniq_723705d1f43f82d');
        $this->addSql('ALTER TABLE transaction ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD wallet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD fiat_currency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD crypto_currency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD confirmation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction DROP user_id_id');
        $this->addSql('ALTER TABLE transaction DROP wallet_id_id');
        $this->addSql('ALTER TABLE transaction DROP fiat_currency_id_id');
        $this->addSql('ALTER TABLE transaction DROP crypto_currency_id_id');
        $this->addSql('ALTER TABLE transaction DROP confirmation_id_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1C4F47010 FOREIGN KEY (fiat_currency_id) REFERENCES fiat_currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D119932572 FOREIGN KEY (crypto_currency_id) REFERENCES crypto_currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D16BACE54E FOREIGN KEY (confirmation_id) REFERENCES payment_confirmation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_723705D1A76ED395 ON transaction (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D1712520F3 ON transaction (wallet_id)');
        $this->addSql('CREATE INDEX IDX_723705D1C4F47010 ON transaction (fiat_currency_id)');
        $this->addSql('CREATE INDEX IDX_723705D119932572 ON transaction (crypto_currency_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D16BACE54E ON transaction (confirmation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1A76ED395');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1712520F3');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1C4F47010');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D119932572');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D16BACE54E');
        $this->addSql('DROP INDEX IDX_723705D1A76ED395');
        $this->addSql('DROP INDEX UNIQ_723705D1712520F3');
        $this->addSql('DROP INDEX IDX_723705D1C4F47010');
        $this->addSql('DROP INDEX IDX_723705D119932572');
        $this->addSql('DROP INDEX UNIQ_723705D16BACE54E');
        $this->addSql('ALTER TABLE transaction ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD wallet_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD fiat_currency_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD crypto_currency_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD confirmation_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction DROP user_id');
        $this->addSql('ALTER TABLE transaction DROP wallet_id');
        $this->addSql('ALTER TABLE transaction DROP fiat_currency_id');
        $this->addSql('ALTER TABLE transaction DROP crypto_currency_id');
        $this->addSql('ALTER TABLE transaction DROP confirmation_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d19d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d1b0c16b0e FOREIGN KEY (crypto_currency_id_id) REFERENCES crypto_currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d1e02c8dfb FOREIGN KEY (confirmation_id_id) REFERENCES payment_confirmation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d1ef882977 FOREIGN KEY (fiat_currency_id_id) REFERENCES fiat_currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d1f43f82d FOREIGN KEY (wallet_id_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_723705d19d86650f ON transaction (user_id_id)');
        $this->addSql('CREATE INDEX idx_723705d1b0c16b0e ON transaction (crypto_currency_id_id)');
        $this->addSql('CREATE INDEX idx_723705d1ef882977 ON transaction (fiat_currency_id_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_723705d1e02c8dfb ON transaction (confirmation_id_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_723705d1f43f82d ON transaction (wallet_id_id)');
    }
}
