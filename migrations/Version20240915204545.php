<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240915204545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demandes_conges DROP FOREIGN KEY demandes_conges_ibfk_1');
        $this->addSql('ALTER TABLE demandes_conges DROP FOREIGN KEY demandes_conges_ibfk_2');
        $this->addSql('DROP INDEX employe_id ON demandes_conges');
        $this->addSql('DROP INDEX manager_id ON demandes_conges');
        $this->addSql('ALTER TABLE demandes_conges CHANGE manager_id manager_id INT NOT NULL, CHANGE commentaire commentaire VARCHAR(50) DEFAULT NULL, CHANGE statut statut VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE employes DROP FOREIGN KEY employes_ibfk_1');
        $this->addSql('DROP INDEX departement_id ON employes');
        $this->addSql('ALTER TABLE managers CHANGE email string VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE managers CHANGE string email VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE demandes_conges CHANGE commentaire commentaire TEXT DEFAULT NULL, CHANGE statut statut VARCHAR(20) DEFAULT NULL, CHANGE manager_id manager_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demandes_conges ADD CONSTRAINT demandes_conges_ibfk_1 FOREIGN KEY (employe_id) REFERENCES employes (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demandes_conges ADD CONSTRAINT demandes_conges_ibfk_2 FOREIGN KEY (manager_id) REFERENCES managers (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX employe_id ON demandes_conges (employe_id)');
        $this->addSql('CREATE INDEX manager_id ON demandes_conges (manager_id)');
        $this->addSql('ALTER TABLE employes ADD CONSTRAINT employes_ibfk_1 FOREIGN KEY (departement_id) REFERENCES departments (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX departement_id ON employes (departement_id)');
    }
}
