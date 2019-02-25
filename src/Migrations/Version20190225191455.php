<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190225191455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE contact (
            id INT AUTO_INCREMENT NOT NULL, 
            first_name VARCHAR(255) DEFAULT NULL, 
            last_name VARCHAR(255) DEFAULT NULL, 
            email_address VARCHAR(255) DEFAULT NULL, 
            profile_photo VARCHAR(255) DEFAULT NULL, 
            favourite TINYINT(1) NOT NULL, 
            created_at DATETIME NOT NULL, 
            updated_at DATETIME NOT NULL, 
            FULLTEXT INDEX IDX_4C62E638A9D1C132C808BA5AB08E074E (first_name, last_name, email_address),
            PRIMARY KEY(id)
          ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql("CREATE TABLE contact_phone_number (
            id INT AUTO_INCREMENT NOT NULL, 
            contact_id INT NOT NULL, 
            number VARCHAR(35) NOT NULL COMMENT '(DC2Type:phone_number)', 
            label VARCHAR(255) DEFAULT NULL, 
            created_at DATETIME NOT NULL, 
            updated_at DATETIME NOT NULL, 
            INDEX IDX_47B68E6FE7A1254A (contact_id), 
            FULLTEXT INDEX IDX_47B68E6F96901F54EA750E8 (number, label),
            PRIMARY KEY(id)
          ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB"
        );

        $this->addSql(
            'ALTER TABLE contact_phone_number ADD CONSTRAINT FK_47B68E6FE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE contact_phone_number DROP FOREIGN KEY FK_47B68E6FE7A1254A');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE contact_phone_number');
    }
}
