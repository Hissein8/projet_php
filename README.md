# Application Web de Gestion (PHP / MySQL)

## Présentation
Ce projet est une application web dynamique permettant la gestion de données (CRUD) via une interface utilisateur intuitive. Il met en pratique l'interaction entre un serveur backend en **PHP** et une base de données relationnelle **MySQL**.

## Fonctionnalités
* **Interface CRUD :** Création, lecture, mise à jour et suppression d'enregistrements.
* **Connexion Sécurisée :** Utilisation de l'interface **PDO** pour interagir avec MySQL.
* **Gestion de Base de Données :** Script SQL inclus pour la création de la structure des tables.
* **Validation de Formulaires :** Traitement des données côté serveur.

## Stack Technique
* **Backend :** PHP 8.x
* **Base de données :** MySQL
* **Frontend :** HTML5 / CSS3 / JAVASCRIPT
* **Sécurité :** Requêtes préparées (Protection contre les injections SQL) / Utilsation de session, de password_hash(), ...

## Installation
1. **Clonage :**
   ```bash
   git clone https://github.com/Hissein8/projet_php.git
2. **configuration**
    créer un fichier de configuration et y définir le chemin de la base de données, son nom, le mot de passe et l'utilisateur.
    importer le fichier .sql présent dans le répository dans votre gestionnaire de base de données locale
3. **lancement**
    placer le dossier dans votre serveur local (XAMPP, WAMPP, etc)
    accédez-y via localhost/projet_php