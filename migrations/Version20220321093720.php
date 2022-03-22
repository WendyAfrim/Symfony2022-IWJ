<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220321093720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create customer entity and join to brand and car';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE car_customer (car_id INT NOT NULL, customer_id INT NOT NULL, PRIMARY KEY(car_id, customer_id))');
        $this->addSql('CREATE INDEX IDX_E0FBB0DC3C6F69F ON car_customer (car_id)');
        $this->addSql('CREATE INDEX IDX_E0FBB0D9395C3F3 ON car_customer (customer_id)');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE car_customer ADD CONSTRAINT FK_E0FBB0DC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE car_customer ADD CONSTRAINT FK_E0FBB0D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE car ADD brand_id INT NOT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_773DE69D44F5D008 ON car (brand_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE car_customer DROP CONSTRAINT FK_E0FBB0D9395C3F3');
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('DROP TABLE car_customer');
        $this->addSql('DROP TABLE customer');
        $this->addSql('ALTER TABLE car DROP CONSTRAINT FK_773DE69D44F5D008');
        $this->addSql('DROP INDEX IDX_773DE69D44F5D008');
        $this->addSql('ALTER TABLE car DROP brand_id');
    }
}
