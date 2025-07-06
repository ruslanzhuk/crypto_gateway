<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706111012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'NOT NULL';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('ALTER TABLE payment_confirmation ALTER confirmed_by_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN payment_confirmation.confirmed_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE transaction ALTER main_status_id SET NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER manual_status_id SET NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER wallet_id SET NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER fiat_currency_id SET NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER crypto_currency_id SET NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER confirmation_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN transaction.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ALTER role_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE wallet ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE wallet ALTER network_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN wallet.user_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE payment_confirmation ALTER confirmed_by_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN payment_confirmation.confirmed_by_id IS NULL');
        $this->addSql('ALTER TABLE wallet ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE wallet ALTER network_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN wallet.user_id IS NULL');
        $this->addSql('ALTER TABLE transaction ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER wallet_id DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER fiat_currency_id DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER crypto_currency_id DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER main_status_id DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER manual_status_id DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER confirmation_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN transaction.user_id IS NULL');
        $this->addSql('ALTER TABLE "user" ALTER role_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".id IS NULL');
    }
}
