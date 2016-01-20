<?php
/**
 * Event's folder class
 *
 * PHP version 5
 *
 * @category Dossier
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Event's folder class
 *
 * PHP version 5
 *
 * @category Dossier
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Dossier
{
    /**
     * Open folder datas
     * @var array
     */
    public $dossier = [];

    /**
     * Database PDO link
     * @var object
     */
    private $_link;

    /**
     * Constructor : open a folder or create it
     *
     * @param string|array $dossier  folder id or args to create it
     * @param boolean      $creation true if it's a creation
     *
     * @return void
     */
    public function __construct($dossier, $creation = false)
    {
        // On commence par paramétrer les données PDO
        $this->_link = Configuration::read('db.link');

        if ($creation) {
             // On prépare la requête
             $query = 'INSERT INTO `dossiers` (`dossier_nom`,
                                               `dossier_description`,
                                               `dossier_date_ouverture`)
                       VALUES (:nom,
                               :desc,
                               NOW())';
             $query = $this->_link->prepare($query);
             $query->bindParam(':nom', $dossier['nom']);
             $query->bindParam(':desc', $dossier['desc']);

             // On exécute la création du dossier
             $query->execute();

             // On récupère l'identifiant du dossier
             $dossier = md5($this->_link->lastInsertId());

             // On vide les informations de BDD
             unset($query);
        }

        // On récupère les informations sur le dossier
        $query = 'SELECT *
                  FROM `dossiers`
                  WHERE MD5(`dossier_id`) = :id';
        $query = $this->_link->prepare($query);
        $query->bindParam(':id', $dossier);
        $query->execute();
        $dossier = $query->fetch(PDO::FETCH_ASSOC);

        // On fabrique de MD5 du dossier
        $dossier['dossier_md5'] = md5($dossier['dossier_id']);

        // On déplace ces informations dans la propriété $dossier
        $this->dossier = $dossier;
    }

    /**
     * Get a JSON with all informations
     *
     * @return string
     */
    public function json()
    {
        return json_encode($this->dossier);
    }

    /**
     * Get an information
     *
     * @param string $info information to get
     *
     * @return mixed
     */
    public function get(string $info)
    {
        return $this->dossier[$info];
    }

    /**
     * Update an information in the database
     *
     * @param string $info   information to update
     * @param string $valeur new value
     *
     * @return void
     */
    public function modifier(string $info, string $valeur)
    {
        // On prépare la requête de modification
        $sql = 'UPDATE `dossiers`
                SET `' . $info . '` = :valeur
                WHERE `dossier_id` = :id';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':id', $this->dossier['dossier_id']);
        $query->bindParam(':valeur', $valeur);

        // On exécute la modification
        $query->execute();
    }

    /**
     * All events related to this folder
     *
     * @return array events IDs
     */
    public function evenements()
    {
        // On prépare la requête
        $sql = 'SELECT `historique_id`
                FROM `historique`
                WHERE `dossier_id` = :id
                ORDER BY `historique_date`
                DESC';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':id', $this->dossier['dossier_id']);

        // On exécute la recherche
        $query->execute();

        // On affecte la requête au tableau $evenements
        $evenements = $query->fetchAll(PDO::FETCH_ASSOC);

        // On retourne le tableau
        return $evenements;
    }

    /**
     * Get all folder's tasks
     *
     * @param integer $statut tasks status
     *
     * @return array tasks array with all informations
     */
    public function taches($statut = 0)
    {
        // On prépare la liste des tâches
        $taches = array();

        // On fait la liste des événements pour récupérer la liste des tâches
        // correspondantes à chaque événement
        $evenements = $this->evenements();

        // Pour chaque événement, on cherche les tâches
        foreach ($evenements as $evenement) {
            // On prépare la requête
            $sql = 'SELECT *
                    FROM `taches`
                    WHERE `historique_id` = :historique
                    AND `tache_terminee` = :statut';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':historique', $evenement['historique_id']);
            $query->bindParam(':statut', $statut);

            // On exécute la requête
            $query->execute();

            // On ajoute les informations à la table
            if ($query->rowCount()) {
                $taches = array_merge(
                    $taches,
                    $query->fetchAll(PDO::FETCH_ASSOC)
                );
            }
        }

        // On retourne la liste des tâches
        return $taches;
    }

    /**
     * List all files related to this folder
     *
     * @return array
     */
    public function fichiers()
    {
        // On prépare la liste des tâches
        $fichiers = array();

        // On fait la liste des événements pour récupérer la liste des tâches
        // correspondantes à chaque événement
        $evenements = $this->evenements();

        // Pour chaque événement, on cherche les tâches
        foreach ($evenements as $evenement) {
            // On prépare la requête
            $sql = 'SELECT *
                    FROM `fichiers`
                    WHERE `interaction_id` = :historique';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':historique', $evenement['historique_id']);

            // On exécute la requête
            $query->execute();

            // On ajoute les informations à la table
            if ($query->rowCount()) {
                $fichiers = array_merge(
                    $fichiers,
                    $query->fetchAll(PDO::FETCH_ASSOC)
                );
            }
        }

        // On retourne la liste des tâches
        return $fichiers;
    }

    /**
     * Return of list of folders for a status
     *
     * @param integer $statut asked folder status
     *
     * @return array
     * @static
     */
    public static function liste($statut = 1)
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');

        // On prépare la requête
        $sql = 'SELECT `dossier_id`
                FROM `dossiers`
                WHERE `dossier_statut` = :statut
                ORDER BY `dossier_nom` ASC';
        $query = $link->prepare($sql);
        $query->bindParam(':statut', $statut, PDO::PARAM_INT);

        // On récupère les données
        $query->execute();
        $dossiers = $query->fetchAll(PDO::FETCH_ASSOC);

        // On retourne le tableau
        return $dossiers;
    }

    /**
     * List all existing folders
     *
     * @param boolean $tous true for all folders, false for all open folders only
     *
     * @return array
     * @static
     */
    public static function listeComplete($tous = false)
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');

        // On lance la recherche des dossiers selon le critère choisi
        if ($tous) {
            $sql = 'SELECT *
                    FROM `dossiers`
                    ORDER BY `dossier_nom` ASC';
        } else {
            $sql = 'SELECT *
                    FROM `dossiers`
                    WHERE `dossier_date_fermeture` IS NULL
                    ORDER BY `dossier_nom` ASC';
        }

        $query = $link->query($sql);

        // On retourne les informations
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
