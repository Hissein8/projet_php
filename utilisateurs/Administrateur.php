<?php
class Administrateur
{
    // Attributs de la classe Administrateur
    private $bd;

    // Constructeur de la classe Administrateur
    public function __construct($bd)
    {
        $this->bd = $bd;
    }

    // CRUD = Create, Read, Update, Delete

    // Create (Créer un nouvel utilisateur)
    public function create($nom, $prenom, $login, $password, $role)
    {
        try {
            $sql = "INSERT INTO utilisateurs (nom, prenom, login, password, role)
            VALUES(:nom, :prenom, :login, :password, :role)";

            // Préparer la requête SQL
            $stmt = $this->bd->prepare($sql);

            // Executer la requête avec les paramètres
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':login' => $login,
                ':password' => $password,
                ':role' => $role
            ]);

            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Read (Lire les informations d'un utilisateur)
    public function findByLogin($login)
    {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE login = :login";

            $stmt = $this->bd->prepare($sql);

            $stmt->execute([':login' => $login]);

            return $stmt->fetch();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Update (Mettre à jour les informations d'un utilisateur)
}
