<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220326120117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

//        $this->addSql('CREATE SCHEMA public');
//        $this->addSql('ALTER TABLE car DROP CONSTRAINT FK_773DE69D44F5D008');
//        $this->addSql('ALTER TABLE car_customer DROP CONSTRAINT FK_E0FBB0DC3C6F69F');
//        $this->addSql('ALTER TABLE car_customer DROP CONSTRAINT FK_E0FBB0D9395C3F3');
//        $this->addSql('DROP SEQUENCE brand_id_seq CASCADE');
//        $this->addSql('DROP SEQUENCE car_id_seq CASCADE');
//        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
//        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
//        $this->addSql('DROP TABLE brand');
//        $this->addSql('DROP TABLE car');
//        $this->addSql('DROP TABLE car_customer');
//        $this->addSql('DROP TABLE customer');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, birthday VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE item (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE to_do_list (id INT NOT NULL, valid_user_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4A6048EC6625D3D0 ON to_do_list (valid_user_id)');
        $this->addSql('ALTER TABLE to_do_list ADD CONSTRAINT FK_4A6048EC6625D3D0 FOREIGN KEY (valid_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE brand_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP SEQUENCE to_do_list_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('CREATE TABLE brand (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE car (id INT NOT NULL, brand_id INT NOT NULL, model VARCHAR(255) DEFAULT NULL, horse_power INT DEFAULT NULL, matriculation VARCHAR(11) NOT NULL, matriculation_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_773DE69D44F5D008 ON car (brand_id)');
        $this->addSql('CREATE TABLE car_customer (car_id INT NOT NULL, customer_id INT NOT NULL, PRIMARY KEY(car_id, customer_id))');
        $this->addSql('CREATE INDEX IDX_E0FBB0DC3C6F69F ON car_customer (car_id)');
        $this->addSql('CREATE INDEX IDX_E0FBB0D9395C3F3 ON car_customer (customer_id)');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE to_do_list DROP CONSTRAINT FK_4A6048EC6625D3D0');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE to_do_list');
    }
}
