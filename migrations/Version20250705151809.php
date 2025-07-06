<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250705151809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crypto_currency DROP CONSTRAINT fk_59320b70b15e270b');
        $this->addSql('DROP INDEX idx_59320b70b15e270b');
        $this->addSql('ALTER TABLE crypto_currency ADD network_id INT NOT NULL');
        $this->addSql('ALTER TABLE crypto_currency DROP network_id_id');
        $this->addSql('ALTER TABLE crypto_currency ADD CONSTRAINT FK_59320B7034128B91 FOREIGN KEY (network_id) REFERENCES network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_59320B7034128B91 ON crypto_currency (network_id)');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT fk_7c68921f9d86650f');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT fk_7c68921fb15e270b');
        $this->addSql('DROP INDEX idx_7c68921f9d86650f');
        $this->addSql('DROP INDEX idx_7c68921fb15e270b');
        $this->addSql('ALTER TABLE wallet ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wallet ADD network_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wallet DROP user_id_id');
        $this->addSql('ALTER TABLE wallet DROP network_id_id');
        $this->addSql('ALTER TABLE wallet RENAME COLUMN public_adress TO public_address');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F34128B91 FOREIGN KEY (network_id) REFERENCES network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7C68921FA76ED395 ON wallet (user_id)');
        $this->addSql('CREATE INDEX IDX_7C68921F34128B91 ON wallet (network_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE crypto_currency DROP CONSTRAINT FK_59320B7034128B91');
        $this->addSql('DROP INDEX IDX_59320B7034128B91');
        $this->addSql('ALTER TABLE crypto_currency ADD network_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crypto_currency DROP network_id');
        $this->addSql('ALTER TABLE crypto_currency ADD CONSTRAINT fk_59320b70b15e270b FOREIGN KEY (network_id_id) REFERENCES network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_59320b70b15e270b ON crypto_currency (network_id_id)');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT FK_7C68921FA76ED395');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT FK_7C68921F34128B91');
        $this->addSql('DROP INDEX IDX_7C68921FA76ED395');
        $this->addSql('DROP INDEX IDX_7C68921F34128B91');
        $this->addSql('ALTER TABLE wallet ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wallet ADD network_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wallet DROP user_id');
        $this->addSql('ALTER TABLE wallet DROP network_id');
        $this->addSql('ALTER TABLE wallet RENAME COLUMN public_address TO public_adress');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT fk_7c68921f9d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT fk_7c68921fb15e270b FOREIGN KEY (network_id_id) REFERENCES network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7c68921f9d86650f ON wallet (user_id_id)');
        $this->addSql('CREATE INDEX idx_7c68921fb15e270b ON wallet (network_id_id)');
    }
}
