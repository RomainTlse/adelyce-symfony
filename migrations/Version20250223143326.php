<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223143326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_article ADD associated_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE basket_article ADD CONSTRAINT FK_C69D1E7FBC272CD1 FOREIGN KEY (associated_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_C69D1E7FBC272CD1 ON basket_article (associated_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_article DROP FOREIGN KEY FK_C69D1E7FBC272CD1');
        $this->addSql('DROP INDEX IDX_C69D1E7FBC272CD1 ON basket_article');
        $this->addSql('ALTER TABLE basket_article DROP associated_user_id');
    }
}
