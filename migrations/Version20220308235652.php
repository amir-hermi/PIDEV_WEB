<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220308235652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, start DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, all_day TINYINT(1) DEFAULT NULL, background_color VARCHAR(7) DEFAULT NULL, border_color VARCHAR(7) DEFAULT NULL, text_color VARCHAR(7) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_reclamation (id INT AUTO_INCREMENT NOT NULL, type ENUM(\'Retard Commande\',\'Changement Produit\',\'Commande Incorrecte\'), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, mission_id INT DEFAULT NULL, date_creation DATE NOT NULL, status ENUM(\'Confirmée\', \'Annulée\',\'En attente\',\'En cours de preparation\',\'Livraison en cours\',\'livrée\'), montant DOUBLE PRECISION DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, notif_client TINYINT(1) DEFAULT NULL, notif_admin TINYINT(1) DEFAULT NULL, INDEX IDX_6EEAA67DFB88E14F (utilisateur_id), INDEX IDX_6EEAA67DBE6CAE90 (mission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_produit (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, commande_id INT DEFAULT NULL, quantite_produit INT DEFAULT NULL, INDEX IDX_DF1E9E87F347EFB (produit_id), INDEX IDX_DF1E9E8782EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commandestock (id INT AUTO_INCREMENT NOT NULL, fournisseur_id INT DEFAULT NULL, date DATE DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, quantite VARCHAR(255) DEFAULT NULL, INDEX IDX_7D7F2798670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commandestock_produit (commandestock_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_50A43E668F3EF2CD (commandestock_id), INDEX IDX_50A43E66F347EFB (produit_id), PRIMARY KEY(commandestock_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commantaire (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, texte VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_93BF4CAFFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorie (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_7DE77163FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorie_produit (favorie_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_43957ED9249A8F58 (favorie_id), INDEX IDX_43957ED9F347EFB (produit_id), PRIMARY KEY(favorie_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fournisseur (id INT AUTO_INCREMENT NOT NULL, adresse_fournisseur VARCHAR(255) DEFAULT NULL, mdp_fournisseur VARCHAR(255) DEFAULT NULL, nom_fournisseur VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mission (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, date DATE NOT NULL, status VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, INDEX IDX_9067F23CFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_24CC0DF2FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, marque_id INT DEFAULT NULL, sous_categire_id INT DEFAULT NULL, prix DOUBLE PRECISION DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, quantite INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, taille ENUM(\'XL\',\'M\',\'XXL\',\'L\',\'S\',\'XS\',\'XXXL\'), description VARCHAR(255) DEFAULT NULL, INDEX IDX_29A5EC274827B9B2 (marque_id), INDEX IDX_29A5EC279EE9E5EB (sous_categire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panierproduit (produit_id INT NOT NULL, panier_id INT NOT NULL, INDEX IDX_656FE9BAF347EFB (produit_id), INDEX IDX_656FE9BAF77D927C (panier_id), PRIMARY KEY(produit_id, panier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, categorie_reclamation_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CE606404BB61C5B6 (categorie_reclamation_id), INDEX IDX_CE606404FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_categorie (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, libelle VARCHAR(255) DEFAULT NULL, INDEX IDX_52743D7BBCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) DEFAULT NULL, etat ENUM(\'Bloquer\',\'Debloquer\'), email VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, activation_token VARCHAR(50) DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, activetoken VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commandestock ADD CONSTRAINT FK_7D7F2798670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commandestock_produit ADD CONSTRAINT FK_50A43E668F3EF2CD FOREIGN KEY (commandestock_id) REFERENCES commandestock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commandestock_produit ADD CONSTRAINT FK_50A43E66F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commantaire ADD CONSTRAINT FK_93BF4CAFFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE favorie ADD CONSTRAINT FK_7DE77163FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE favorie_produit ADD CONSTRAINT FK_43957ED9249A8F58 FOREIGN KEY (favorie_id) REFERENCES favorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorie_produit ADD CONSTRAINT FK_43957ED9F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC274827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC279EE9E5EB FOREIGN KEY (sous_categire_id) REFERENCES sous_categorie (id)');
        $this->addSql('ALTER TABLE panierproduit ADD CONSTRAINT FK_656FE9BAF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panierproduit ADD CONSTRAINT FK_656FE9BAF77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404BB61C5B6 FOREIGN KEY (categorie_reclamation_id) REFERENCES categorie_reclamation (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE sous_categorie ADD CONSTRAINT FK_52743D7BBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sous_categorie DROP FOREIGN KEY FK_52743D7BBCF5E72D');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404BB61C5B6');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8782EA2E54');
        $this->addSql('ALTER TABLE commandestock_produit DROP FOREIGN KEY FK_50A43E668F3EF2CD');
        $this->addSql('ALTER TABLE favorie_produit DROP FOREIGN KEY FK_43957ED9249A8F58');
        $this->addSql('ALTER TABLE commandestock DROP FOREIGN KEY FK_7D7F2798670C757F');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC274827B9B2');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DBE6CAE90');
        $this->addSql('ALTER TABLE panierproduit DROP FOREIGN KEY FK_656FE9BAF77D927C');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB');
        $this->addSql('ALTER TABLE commandestock_produit DROP FOREIGN KEY FK_50A43E66F347EFB');
        $this->addSql('ALTER TABLE favorie_produit DROP FOREIGN KEY FK_43957ED9F347EFB');
        $this->addSql('ALTER TABLE panierproduit DROP FOREIGN KEY FK_656FE9BAF347EFB');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC279EE9E5EB');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB88E14F');
        $this->addSql('ALTER TABLE commantaire DROP FOREIGN KEY FK_93BF4CAFFB88E14F');
        $this->addSql('ALTER TABLE favorie DROP FOREIGN KEY FK_7DE77163FB88E14F');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CFB88E14F');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2FB88E14F');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404FB88E14F');
        $this->addSql('DROP TABLE calendar');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE categorie_reclamation');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_produit');
        $this->addSql('DROP TABLE commandestock');
        $this->addSql('DROP TABLE commandestock_produit');
        $this->addSql('DROP TABLE commantaire');
        $this->addSql('DROP TABLE favorie');
        $this->addSql('DROP TABLE favorie_produit');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE mission');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE panierproduit');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE sous_categorie');
        $this->addSql('DROP TABLE utilisateur');
    }
}