-- ============================================================
-- Script SQL - Site d'actualité dynamique
-- ESP Département Génie Informatique - Projet Final Backend
-- ============================================================

-- 1. Création de la base de données
-- ============================================================
CREATE DATABASE IF NOT EXISTS actualites
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE actualites;




-- 2. Table des catégories
-- (créée en premier car articles en dépend)
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
) ENGINE=InnoDB;




-- 3. Table des utilisateurs
-- ============================================================
CREATE TABLE IF NOT EXISTS utilisateurs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    prenom        VARCHAR(100) NOT NULL,
    login         VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL,
    role          ENUM('editeur', 'administrateur') NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Table des articles
-- (créée en dernier car elle dépend des deux tables ci-dessus)
-- ============================================================


CREATE TABLE IF NOT EXISTS articles (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    titre              VARCHAR(255) NOT NULL,
    description_courte TEXT NOT NULL,
    contenu            TEXT NOT NULL,
    categorie_id       INT NOT NULL,
    editeur_id         INT NOT NULL,
    image_url          VARCHAR(255),
    date_publication   DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (editeur_id)   REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 5. Données de test
-- ============================================================

-- Catégories
INSERT INTO categories (nom) VALUES
('Technologie'),
('Sport'),
('Politique'),
('Éducation'),
('Culture');

-- Utilisateurs
-- admin     → mot de passe : admin123
-- editeur1  → mot de passe : edit123
INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Diop', 'Amadou', 'diopamadou','password1','administrateur'),
('Fall', 'Fatou', 'fallfatou','password2','editeur'),
('Ba', 'Mamadou', 'bamamadou','password3','editeur');

-- Articles de test
INSERT INTO articles (titre, description_courte, contenu, categorie_id, editeur_id, image_url) VALUES
(
    'L\'IA transforme le monde du travail',
    'L\'intelligence artificielle change profondément les métiers modernes.',
    'L\'intelligence artificielle est en train de transformer de nombreux secteurs professionnels. Des études montrent que plus de 40% des tâches répétitives pourraient être automatisées dans les prochaines années. Cependant, de nouveaux métiers émergent également, nécessitant des compétences en gestion et en supervision des systèmes intelligents. Les entreprises investissent massivement dans la formation de leurs employés pour s\'adapter à cette révolution numérique.',
    1, 2, 'https://example.com/images/ia_travail.jpg'
),
(
    'Dakar se prépare pour la CAN',
    'Le Sénégal renforce ses infrastructures sportives pour accueillir les meilleures équipes africaines.',
    'Le Sénégal s\'apprête à vivre une grande fête du football. Les infrastructures sportives sont en cours de rénovation pour accueillir les meilleures équipes africaines. Des milliers de supporters sont attendus dans la capitale sénégalaise. Le comité d\'organisation a annoncé que tous les stades seront prêts dans les délais impartis.',
    2, 2, 'https://example.com/images/can_dakar.jpg'
),
(
    'Réforme du système éducatif annoncée',
    'Le ministère présente un plan ambitieux pour moderniser l\'école sénégalaise.',
    'Le ministère de l\'Éducation nationale a présenté son plan de réforme pour moderniser le système scolaire. Les principales mesures incluent l\'introduction du numérique dès le primaire, la révision des programmes et une meilleure formation des enseignants. Cette réforme touchera plus de 3 millions d\'élèves à travers le pays.',
    4, 3, 'https://example.com/images/reforme_education.jpg'
),
(
    'Festival de jazz de Saint-Louis',
    'Le célèbre festival international revient pour une nouvelle édition exceptionnelle.',
    'La ville de Saint-Louis se prépare à accueillir son festival annuel de jazz. Cette année, des artistes venus de plus de 20 pays se produiront sur les scènes de la ville. Le festival est une occasion de célébrer la richesse de la culture africaine et internationale. Plus de 50 000 spectateurs sont attendus sur les trois jours de l\'événement.',
    5, 3, 'https://example.com/images/festival_jazz.jpg'
),
(
    'Nouvelles mesures climatiques adoptées',
    'Le Sénégal renforce ses engagements pour la protection de l\'environnement.',
    'Lors du dernier sommet national sur le climat, plusieurs nouvelles mesures ont été adoptées pour réduire les émissions de carbone au Sénégal. Parmi celles-ci, le développement des énergies renouvelables et la protection des zones forestières occupent une place centrale. Le gouvernement s\'engage à atteindre 30% d\'énergies renouvelables d\'ici 2030.',
    3, 2, 'https://example.com/images/mesures_climatiques.jpg'
);
