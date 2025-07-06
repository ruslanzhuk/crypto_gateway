<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706113512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change user.id PK from integer to uuid and update all related foreign keys';
    }

    public function up(Schema $schema): void
    {
        // 1. Встановити uuid-ossp extension (якщо не встановлений)
        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        // 2. Відключити FK constraints, що посилаються на user.id
        $this->addSql('ALTER TABLE payment_confirmation DROP CONSTRAINT IF EXISTS fk_2ecda5266f45385d');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT IF EXISTS fk_723705d1a76ed395');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT IF EXISTS fk_7c68921fa76ed395');

        // 3. Додати нову колонку uuid id_new в таблицю user
        $this->addSql('ALTER TABLE "user" ADD COLUMN id_new UUID DEFAULT uuid_generate_v4()');

        // 4. Додати нові колонки для uuid FK у залежних таблицях
        $this->addSql('ALTER TABLE payment_confirmation ADD COLUMN confirmed_by_id_new UUID');
        $this->addSql('ALTER TABLE transaction ADD COLUMN user_id_new UUID');
        $this->addSql('ALTER TABLE wallet ADD COLUMN user_id_new UUID');

        // 5. Заповнити нові FK колонки uuid зі зв’язком по старому int id
        $this->addSql('
            UPDATE payment_confirmation pc
            SET confirmed_by_id_new = u.id_new
            FROM "user" u
            WHERE pc.confirmed_by_id = u.id
        ');
        $this->addSql('
            UPDATE transaction t
            SET user_id_new = u.id_new
            FROM "user" u
            WHERE t.user_id = u.id
        ');
        $this->addSql('
            UPDATE wallet w
            SET user_id_new = u.id_new
            FROM "user" u
            WHERE w.user_id = u.id
        ');

        // 6. Видалити старі FK колонки, перейменувати нові, встановити NOT NULL
        $this->addSql('ALTER TABLE payment_confirmation DROP COLUMN confirmed_by_id');
        $this->addSql('ALTER TABLE payment_confirmation RENAME COLUMN confirmed_by_id_new TO confirmed_by_id');
        $this->addSql('ALTER TABLE payment_confirmation ALTER confirmed_by_id SET NOT NULL');

        $this->addSql('ALTER TABLE transaction DROP COLUMN user_id');
        $this->addSql('ALTER TABLE transaction RENAME COLUMN user_id_new TO user_id');
        $this->addSql('ALTER TABLE transaction ALTER user_id SET NOT NULL');

        $this->addSql('ALTER TABLE wallet DROP COLUMN user_id');
        $this->addSql('ALTER TABLE wallet RENAME COLUMN user_id_new TO user_id');
        $this->addSql('ALTER TABLE wallet ALTER user_id SET NOT NULL');

        // 7. Видалити старий int id, перейменувати uuid id_new в id, зробити PK
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT IF EXISTS user_pkey');
        $this->addSql('ALTER TABLE "user" DROP COLUMN id');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN id_new TO id');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (id)');

        // 8. Відновити FK constraints на user.id (тип UUID)
        $this->addSql('
            ALTER TABLE payment_confirmation
            ADD CONSTRAINT fk_2ecda5266f45385d FOREIGN KEY (confirmed_by_id) REFERENCES "user"(id)
        ');
        $this->addSql('
            ALTER TABLE transaction
            ADD CONSTRAINT fk_723705d1a76ed395 FOREIGN KEY (user_id) REFERENCES "user"(id)
        ');
        $this->addSql('
            ALTER TABLE wallet
            ADD CONSTRAINT fk_7c68921fa76ed395 FOREIGN KEY (user_id) REFERENCES "user"(id)
        ');
    }

    public function down(Schema $schema): void
    {
        // Якщо потрібно відкотитися назад до integer PK — це теж складно,
        // і тут показана базова логіка, але треба бути дуже обережним

        $this->addSql('ALTER TABLE payment_confirmation DROP CONSTRAINT IF EXISTS fk_2ecda5266f45385d');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT IF EXISTS fk_723705d1a76ed395');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT IF EXISTS fk_7c68921fa76ed395');

        $this->addSql('ALTER TABLE payment_confirmation DROP COLUMN confirmed_by_id');
        $this->addSql('ALTER TABLE payment_confirmation ADD COLUMN confirmed_by_id INT');

        $this->addSql('ALTER TABLE transaction DROP COLUMN user_id');
        $this->addSql('ALTER TABLE transaction ADD COLUMN user_id INT');

        $this->addSql('ALTER TABLE wallet DROP COLUMN user_id');
        $this->addSql('ALTER TABLE wallet ADD COLUMN user_id INT');

        // У user зробити те саме
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT IF EXISTS user_pkey');
        $this->addSql('ALTER TABLE "user" DROP COLUMN id');
        $this->addSql('ALTER TABLE "user" ADD COLUMN id SERIAL PRIMARY KEY');

        // Відновити FK (тепер вже на integer)
        $this->addSql('
            ALTER TABLE payment_confirmation
            ADD CONSTRAINT fk_2ecda5266f45385d FOREIGN KEY (confirmed_by_id) REFERENCES "user"(id)
        ');
        $this->addSql('
            ALTER TABLE transaction
            ADD CONSTRAINT fk_723705d1a76ed395 FOREIGN KEY (user_id) REFERENCES "user"(id)
        ');
        $this->addSql('
            ALTER TABLE wallet
            ADD CONSTRAINT fk_7c68921fa76ed395 FOREIGN KEY (user_id) REFERENCES "user"(id)
        ');
    }
}
