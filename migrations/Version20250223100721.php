<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223100721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_article DROP FOREIGN KEY FK_C69D1E7FA76ED395');
        $this->addSql('DROP INDEX IDX_C69D1E7FA76ED395 ON basket_article');
        $this->addSql('DROP INDEX `primary` ON basket_article');
        $this->addSql('ALTER TABLE basket_article DROP user_id');
        $this->addSql('ALTER TABLE basket_article ADD PRIMARY KEY (basket_id, article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON basket_article');
        $this->addSql('ALTER TABLE basket_article ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket_article ADD CONSTRAINT FK_C69D1E7FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C69D1E7FA76ED395 ON basket_article (user_id)');
        $this->addSql('ALTER TABLE basket_article ADD PRIMARY KEY (basket_id, article_id, user_id)');
    }
}
