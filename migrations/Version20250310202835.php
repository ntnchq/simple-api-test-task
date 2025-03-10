<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250310202835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article_tags (
          id INT AUTO_INCREMENT NOT NULL,
          created_at DATETIME NOT NULL,
          updated_at DATETIME NOT NULL,
          article_id INT NOT NULL,
          tag_id INT NOT NULL,
          INDEX IDX_DFFE13277294869C (article_id),
          INDEX IDX_DFFE1327BAD26311 (tag_id),
          UNIQUE INDEX UNIQ_DFFE13277294869CBAD26311 (article_id, tag_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE articles (
          title VARCHAR(255) NOT NULL,
          id INT AUTO_INCREMENT NOT NULL,
          created_at DATETIME NOT NULL,
          updated_at DATETIME NOT NULL,
          INDEX IDX_BFDD31682B36786B (title),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tags (
          name VARCHAR(255) NOT NULL,
          id INT AUTO_INCREMENT NOT NULL,
          created_at DATETIME NOT NULL,
          updated_at DATETIME NOT NULL,
          UNIQUE INDEX UNIQ_6FBC94265E237E06 (name),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE
          article_tags
        ADD
          CONSTRAINT FK_DFFE13277294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE
          article_tags
        ADD
          CONSTRAINT FK_DFFE1327BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article_tags DROP FOREIGN KEY FK_DFFE13277294869C');
        $this->addSql('ALTER TABLE article_tags DROP FOREIGN KEY FK_DFFE1327BAD26311');
        $this->addSql('DROP TABLE article_tags');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE tags');
    }
} 