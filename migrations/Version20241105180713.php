<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241105180713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the article table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article (id uuid NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE article');
    }
}
