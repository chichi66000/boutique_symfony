<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230206140654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD size_id INT NOT NULL, ADD audience_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD498DA827 FOREIGN KEY (size_id) REFERENCES size (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD848CC616 FOREIGN KEY (audience_id) REFERENCES audience (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD498DA827 ON product (size_id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD848CC616 ON product (audience_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD498DA827');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD848CC616');
        $this->addSql('DROP INDEX IDX_D34A04AD498DA827 ON product');
        $this->addSql('DROP INDEX IDX_D34A04AD848CC616 ON product');
        $this->addSql('ALTER TABLE product DROP size_id, DROP audience_id');
    }
}
