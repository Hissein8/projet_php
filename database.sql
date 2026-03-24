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
-- INSERT INTO articles (titre, description_courte, contenu, categorie_id, editeur_id, image_url) VALUES
-- (
--     'L\'IA transforme le monde du travail',
--     'L\'intelligence artificielle change profondément les métiers modernes.',
--     'L\'intelligence artificielle est en train de transformer de nombreux secteurs professionnels. Des études montrent que plus de 40% des tâches répétitives pourraient être automatisées dans les prochaines années. Cependant, de nouveaux métiers émergent également, nécessitant des compétences en gestion et en supervision des systèmes intelligents. Les entreprises investissent massivement dans la formation de leurs employés pour s\'adapter à cette révolution numérique.',
--     1, 2, 'https://example.com/images/ia_travail.jpg'
-- ),
-- (
--     'Dakar se prépare pour la CAN',
--     'Le Sénégal renforce ses infrastructures sportives pour accueillir les meilleures équipes africaines.',
--     'Le Sénégal s\'apprête à vivre une grande fête du football. Les infrastructures sportives sont en cours de rénovation pour accueillir les meilleures équipes africaines. Des milliers de supporters sont attendus dans la capitale sénégalaise. Le comité d\'organisation a annoncé que tous les stades seront prêts dans les délais impartis.',
--     2, 2, 'https://example.com/images/can_dakar.jpg'
-- ),
-- (
--     'Réforme du système éducatif annoncée',
--     'Le ministère présente un plan ambitieux pour moderniser l\'école sénégalaise.',
--     'Le ministère de l\'Éducation nationale a présenté son plan de réforme pour moderniser le système scolaire. Les principales mesures incluent l\'introduction du numérique dès le primaire, la révision des programmes et une meilleure formation des enseignants. Cette réforme touchera plus de 3 millions d\'élèves à travers le pays.',
--     4, 3, 'https://example.com/images/reforme_education.jpg'
-- ),
-- (
--     'Festival de jazz de Saint-Louis',
--     'Le célèbre festival international revient pour une nouvelle édition exceptionnelle.',
--     'La ville de Saint-Louis se prépare à accueillir son festival annuel de jazz. Cette année, des artistes venus de plus de 20 pays se produiront sur les scènes de la ville. Le festival est une occasion de célébrer la richesse de la culture africaine et internationale. Plus de 50 000 spectateurs sont attendus sur les trois jours de l\'événement.',
--     5, 3, 'https://example.com/images/festival_jazz.jpg'
-- ),
-- (
--     'Nouvelles mesures climatiques adoptées',
--     'Le Sénégal renforce ses engagements pour la protection de l\'environnement.',
--     'Lors du dernier sommet national sur le climat, plusieurs nouvelles mesures ont été adoptées pour réduire les émissions de carbone au Sénégal. Parmi celles-ci, le développement des énergies renouvelables et la protection des zones forestières occupent une place centrale. Le gouvernement s\'engage à atteindre 30% d\'énergies renouvelables d\'ici 2030.',
--     3, 2, 'https://example.com/images/mesures_climatiques.jpg'
-- );


-- ============================================================
--  Articles avec vraies images Unsplash (libres de droits)
--  Adapter categorie_id et editeur_id selon votre base
-- ============================================================

INSERT INTO articles (titre, description_courte, contenu, categorie_id, editeur_id, image_url) VALUES

-- ── Technologie ──────────────────────────────────────────
(
    'L\'IA transforme le monde du travail',
    'L\'intelligence artificielle change profondément les métiers modernes.',
    'L\'intelligence artificielle est en train de transformer de nombreux secteurs professionnels. Des études montrent que plus de 40 % des tâches répétitives pourraient être automatisées dans les prochaines années. Cependant, de nouveaux métiers émergent également, nécessitant des compétences en gestion et en supervision des systèmes intelligents. Les entreprises investissent massivement dans la formation de leurs employés pour s\'adapter à cette révolution numérique. Les experts s\'accordent à dire que l\'humain restera au cœur des décisions stratégiques, tandis que l\'IA prendra en charge les processus à forte répétition.',
    1, 2,
    'https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=1200&q=80'
    -- Photo : robot humanoïde lumineux sur fond sombre — Unsplash @Steve Johnson
),

(
    'ChatGPT et les grands modèles de langage : où en est-on ?',
    'Les LLM redéfinissent notre rapport à l\'information et à la création de contenu.',
    'Depuis le lancement de ChatGPT fin 2022, les grands modèles de langage (LLM) n\'ont cessé de progresser. En 2024, des modèles comme GPT-4, Gemini ou Claude rivalisent sur des benchmarks de raisonnement complexe. Ces outils transforment la rédaction, la programmation, le service client et même la recherche médicale. Mais des questions éthiques persistent : biais algorithmiques, désinformation générée automatiquement, et impact sur l\'emploi des créatifs. Les régulateurs européens ont adopté l\'AI Act pour encadrer ces technologies, une première mondiale.',
    1, 2,
    'https://images.unsplash.com/photo-1655720828018-edd2daec9349?w=1200&q=80'
    -- Photo : écran avec code et visualisation IA — Unsplash @Growtika
),

-- ── Sport ────────────────────────────────────────────────
(
    'Dakar se prépare pour la CAN 2025',
    'Le Sénégal renforce ses infrastructures sportives pour accueillir les meilleures équipes africaines.',
    'Le Sénégal s\'apprête à vivre une grande fête du football. Les infrastructures sportives sont en cours de rénovation pour accueillir les meilleures équipes du continent africain. Des milliers de supporters sont attendus dans la capitale sénégalaise. Le comité d\'organisation a annoncé que tous les stades seront prêts dans les délais impartis. Le Stade Léopold Sédar Senghor a fait l\'objet d\'importants travaux de modernisation, avec une capacité portée à 60 000 places. L\'équipe nationale, récente championne d\'Afrique, entre dans la compétition en tant que favori.',
    2, 2,
    'https://images.unsplash.com/photo-1522778119026-d647f0596c20?w=1200&q=80'
    -- Photo : stade de football vu de nuit sous les projecteurs — Unsplash @Vienna Reyes
),

(
    'Sadio Mané : retour en forme et nouveau défi',
    'L\'attaquant sénégalais retrouve son meilleur niveau après une blessure difficile.',
    'Après plusieurs mois d\'absence suite à une blessure au genou, Sadio Mané est de retour sur les terrains. L\'attaquant de l\'équipe nationale du Sénégal a retrouvé son club en grande forme, enchaînant les buts et les passes décisives. Son retour tombe à pic avant la Coupe d\'Afrique des Nations. Les supporters sénégalais espèrent le voir mener les Lions de la Teranga vers un deuxième titre continental consécutif. L\'attaquant a confié en conférence de presse sa motivation intacte et son désir de marquer l\'histoire du football africain.',
    2, 3,
    'https://images.unsplash.com/photo-1543326727-cf6c39e8f84c?w=1200&q=80'
    -- Photo : joueur de foot en action sur pelouse — Unsplash @Chaos Soccer Gear
),

-- ── Environnement / Santé ────────────────────────────────
(
    'Nouvelles mesures climatiques adoptées au Sénégal',
    'Le Sénégal renforce ses engagements pour la protection de l\'environnement.',
    'Lors du dernier sommet national sur le climat, plusieurs nouvelles mesures ont été adoptées pour réduire les émissions de carbone au Sénégal. Parmi celles-ci, le développement des énergies renouvelables et la protection des zones forestières occupent une place centrale. Le gouvernement s\'engage à atteindre 30 % d\'énergies renouvelables d\'ici 2030. Des partenariats ont également été signés avec des organisations internationales pour financer la transition énergétique. La jeunesse sénégalaise, très mobilisée sur ces enjeux, a salué ces annonces tout en appelant à des actions concrètes et rapides.',
    3, 2,
    'https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=1200&q=80'
    -- Photo : éoliennes au coucher du soleil — Unsplash @Jason Blackeye
),

(
    'Avancée médicale : un vaccin contre le paludisme homologué',
    'L\'OMS valide un second vaccin contre le paludisme, une maladie qui tue 600 000 personnes par an.',
    'L\'Organisation mondiale de la santé a homologué un nouveau vaccin contre le paludisme, le R21/Matrix-M, développé par l\'Université d\'Oxford. Cette annonce représente un tournant majeur dans la lutte contre l\'une des maladies infectieuses les plus meurtrières au monde, qui touche particulièrement l\'Afrique subsaharienne. Avec une efficacité de 75 % sur les enfants de moins de cinq ans, ce vaccin pourrait sauver des centaines de milliers de vies chaque année. Le Sénégal figure parmi les premiers pays ciblés pour le déploiement à grande échelle du programme de vaccination.',
    3, 3,
    'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=1200&q=80'
    -- Photo : laboratoire médical, seringues et vaccins — Unsplash @Testalize.me
),

-- ── Éducation / Politique ────────────────────────────────
(
    'Réforme du système éducatif sénégalais annoncée',
    'Le ministère présente un plan ambitieux pour moderniser l\'école sénégalaise.',
    'Le ministère de l\'Éducation nationale a présenté son plan de réforme pour moderniser le système scolaire. Les principales mesures incluent l\'introduction du numérique dès le primaire, la révision des programmes et une meilleure formation des enseignants. Cette réforme touchera plus de 3 millions d\'élèves à travers le pays. Un budget exceptionnel de 50 milliards de francs CFA a été alloué pour financer l\'achat de tablettes éducatives et la formation de 20 000 enseignants. Les syndicats ont globalement bien accueilli ces annonces, tout en demandant des garanties sur le calendrier de mise en œuvre.',
    4, 3,
    'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1200&q=80'
    -- Photo : enfants en classe avec cahiers — Unsplash @CDC
),

(
    'Élections locales : forte mobilisation des jeunes électeurs',
    'Les moins de 30 ans représentent désormais plus de la moitié des inscrits sur les listes électorales.',
    'Les prochaines élections locales s\'annoncent comme un test majeur pour la démocratie sénégalaise. Pour la première fois, les moins de 30 ans représentent plus de la moitié des inscrits sur les listes électorales. Cette jeunesse engagée se mobilise sur des enjeux concrets : emploi, logement, accès à l\'eau et aux soins. Les partis politiques ont dû adapter leur communication en conséquence, investissant massivement les réseaux sociaux. Plusieurs candidats indépendants, sans étiquette de parti, se démarquent dans les sondages grâce à leur présence numérique et leurs propositions innovantes.',
    4, 2,
    'https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?w=1200&q=80'
    -- Photo : urne de vote / démocratie — Unsplash @Element5 Digital
),

-- ── Culture ──────────────────────────────────────────────
(
    'Festival de jazz de Saint-Louis : une édition record',
    'Le célèbre festival international revient pour une nouvelle édition exceptionnelle.',
    'La ville de Saint-Louis se prépare à accueillir son festival annuel de jazz. Cette année, des artistes venus de plus de 20 pays se produiront sur les scènes emblématiques de la vieille ville. Le festival est une occasion unique de célébrer la richesse de la culture africaine et internationale. Plus de 50 000 spectateurs sont attendus sur les trois jours de l\'événement. Parmi les têtes d\'affiche, on compte des légendes du jazz africain comme Omar Sosa et Cheick Tidiane Seck, aux côtés de jeunes talents de la scène internationale. Les hôtels affichent complet depuis plusieurs semaines.',
    5, 3,
    'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=1200&q=80'
    -- Photo : concert de jazz, musiciens sur scène avec lumières — Unsplash @Nicholas Green
),

(
    'Le cinéma sénégalais à l\'honneur à Cannes',
    'Deux films sénégalais en compétition officielle pour la première fois de l\'histoire.',
    'Le cinéma sénégalais vit un moment historique. Pour la première fois, deux longs métrages produits au Sénégal sont sélectionnés en compétition officielle au Festival de Cannes. Ces films, portés par de jeunes réalisatrices sénégalaises, explorent des thèmes universels à travers le prisme de la société ouest-africaine contemporaine. Leur présence sur la Croisette témoigne du dynamisme d\'une industrie cinématographique en plein essor, soutenue par de nouvelles politiques publiques de financement de la création. La presse internationale les cite déjà parmi les favoris pour la Palme d\'Or.',
    5, 2,
    'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=1200&q=80'
    -- Photo : salle de cinéma, projecteur et écran — Unsplash @Myke Simon
);