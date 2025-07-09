<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708160853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IF EXISTS IDX_2ECDA5266F45385D');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2ECDA5266F45385D ON payment_confirmation (confirmed_by_id)');
        $this->addSql('ALTER TABLE transaction ALTER confirmation_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER id DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_2ECDA5266F45385D');
        $this->addSql('CREATE INDEX IDX_2ECDA5266F45385D ON payment_confirmation (confirmed_by_id)');
        $this->addSql('ALTER TABLE transaction ALTER confirmation_id SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER id SET DEFAULT \'uuid_generate_v4()\'');
    }
}
