<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250321015336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create post table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE post (
            id INT AUTO_INCREMENT NOT NULL, 
            title VARCHAR(255) NOT NULL, 
            content LONGTEXT NOT NULL, 
            is_published TINYINT(1) DEFAULT 1 NOT NULL, 
            created_at DATETIME NOT NULL, 
            updated_at DATETIME NOT NULL, 
            deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('CREATE TABLE messenger_messages (
            id BIGINT AUTO_INCREMENT NOT NULL, 
            body LONGTEXT NOT NULL, 
            headers LONGTEXT NOT NULL, 
            queue_name VARCHAR(190) NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            INDEX IDX_75EA56E0FB7336F0 (queue_name), 
            INDEX IDX_75EA56E0E3BD61CE (available_at), 
            INDEX IDX_75EA56E016BA31DB (delivered_at), 
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
