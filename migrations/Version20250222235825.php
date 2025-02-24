<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222235825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE basket_article (basket_id INT NOT NULL, article_id INT NOT NULL, user_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_C69D1E7F1BE1FB52 (basket_id), INDEX IDX_C69D1E7F7294869C (article_id), INDEX IDX_C69D1E7FA76ED395 (user_id), PRIMARY KEY(basket_id, article_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE baskets (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, basket_number VARCHAR(12) NOT NULL, dt_created DATETIME NOT NULL, INDEX IDX_DCFB21EFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket_article ADD CONSTRAINT FK_C69D1E7F1BE1FB52 FOREIGN KEY (basket_id) REFERENCES baskets (id)');
        $this->addSql('ALTER TABLE basket_article ADD CONSTRAINT FK_C69D1E7F7294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE basket_article ADD CONSTRAINT FK_C69D1E7FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE baskets ADD CONSTRAINT FK_DCFB21EFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_article DROP FOREIGN KEY FK_C69D1E7F1BE1FB52');
        $this->addSql('ALTER TABLE basket_article DROP FOREIGN KEY FK_C69D1E7F7294869C');
        $this->addSql('ALTER TABLE basket_article DROP FOREIGN KEY FK_C69D1E7FA76ED395');
        $this->addSql('ALTER TABLE baskets DROP FOREIGN KEY FK_DCFB21EFA76ED395');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE basket_article');
        $this->addSql('DROP TABLE baskets');
    }
}
