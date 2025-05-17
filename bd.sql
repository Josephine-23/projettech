CREATE DATABASE IF NOT EXISTS projettech;
USE projettech;
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL, -- Assez long pour les hashs
    role VARCHAR(20) DEFAULT 'utilisateur',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id_categorie INT AUTO_INCREMENT PRIMARY KEY,
  nom_categorie VARCHAR(100) NOT NULL UNIQUE,
  description_categorie TEXT NULL, -- Optionnel
  date_creation_categorie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_modification_categorie TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS fournisseurs (
  id_fournisseur INT AUTO_INCREMENT PRIMARY KEY,
  nom_fournisseur VARCHAR(150) NOT NULL UNIQUE,
  contact_fournisseur VARCHAR(100) NULL, -- Nom de la personne contact
  email_fournisseur VARCHAR(100) NULL,
  telephone_fournisseur VARCHAR(20) NULL,
  adresse_fournisseur TEXT NULL,
  date_creation_fournisseur TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_modification_fournisseur TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS produits (
  id_produit INT AUTO_INCREMENT PRIMARY KEY,
  nom_produit VARCHAR(150) NOT NULL,
  description_produit TEXT NULL, -- Optionnel
  id_categorie INT NOT NULL, -- Clé étrangère vers la table `categories`
  id_fournisseur INT NOT NULL, -- Clé étrangère vers la table `fournisseurs`
  conditionnement VARCHAR(100) NULL, -- Ex: "Bouteille 70cl", "Canette 33cl", "Carton de 12"
  stock_actuel INT DEFAULT 0,
  stock_maximum INT NULL, -- Stock idéal ou capacité de stockage
  seuil_critique INT NULL, -- Seuil à partir duquel une alerte est déclenchée
  prix_achat DECIMAL(10, 2) NULL, -- Optionnel
  prix_vente DECIMAL(10, 2) NULL, -- Optionnel
  -- image_produit VARCHAR(255) NULL, -- Optionnel: chemin vers une image du produit
  date_creation_produit TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_modification_produit TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_categorie) REFERENCES categories(id_categorie) ON DELETE RESTRICT ON UPDATE CASCADE, -- Empêche la suppression d'une catégorie si des produits y sont liés
  FOREIGN KEY (id_fournisseur) REFERENCES fournisseurs(id_fournisseur) ON DELETE RESTRICT ON UPDATE CASCADE -- Empêche la suppression d'un fournisseur si des produits y sont liés
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour améliorer les recherches sur le nom du produit
CREATE INDEX idx_nom_produit ON produits(nom_produit);

CREATE TABLE IF NOT EXISTS inventaires (
  id_inventaire INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL, -- Qui a réalisé l'inventaire
  nom_realisateur VARCHAR(100) NULL, -- Peut être le nom saisi manuellement ou le nom de l'utilisateur
  date_inventaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  notes_inventaire TEXT NULL, -- Notes générales sur l'inventaire
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE -- Si l'utilisateur est supprimé, ses inventaires aussi (ou SET NULL si vous voulez garder l'historique anonymisé)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS details_inventaire (
  id_detail_inventaire INT AUTO_INCREMENT PRIMARY KEY,
  id_inventaire INT NOT NULL, -- Référence à l'inventaire global
  id_produit INT NOT NULL, -- Référence au produit compté
  stock_precedent INT NULL, -- Stock enregistré avant ce comptage (peut venir de produits.stock_actuel ou du précédent inventaire)
  quantite_comptee INT NOT NULL, -- Le nouveau comptage physique
  ecart INT NULL, -- Calculé: quantite_comptee - stock_precedent (si stock_precedent est pertinent)
  date_comptage TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Peut être redondant avec date_inventaire mais utile si comptages partiels
  FOREIGN KEY (id_inventaire) REFERENCES inventaires(id_inventaire) ON DELETE CASCADE ON UPDATE CASCADE, -- Si un inventaire est supprimé, ses détails le sont aussi
  FOREIGN KEY (id_produit) REFERENCES produits(id_produit) ON DELETE CASCADE ON UPDATE CASCADE, -- Si un produit est supprimé, ses entrées d'inventaire aussi
  UNIQUE KEY uq_inventaire_produit (id_inventaire, id_produit) -- S'assurer qu'un produit n'est compté qu'une fois par inventaire
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;