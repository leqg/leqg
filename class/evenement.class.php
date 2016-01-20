<?php
/**
 * Event's class
 *
 * PHP version 5
 *
 * @category Evenement
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Event's class
 *
 * PHP version 5
 *
 * @category Evenement
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Evenement
{
    /**
     * Events data
     * @var array
     */
    private $_evenement = [];

    /**
     * Database PDO object
     * @var object
     */
    private $_link;

    /**
     * Constructor method
     *
     * @param string|integer $evenement event ID
     * @param boolean        $securite  event ID is or not an hash
     * @param boolean        $creation  created?
     *
     * @return void
     */
    public function __construct($evenement, $securite = true, $creation = false)
    {
        // On commence par paramétrer les données PDO
        $this->_link = Configuration::read('db.link');

        // On regarde si on doit créer un nouvel événement,
        // ou s'il s'agit d'un événement à ouvrir
        if ($creation === true) {
            // On prépare les variables
            if (isset($_COOKIE['leqg-user'])) {
                $user = $_COOKIE['leqg-user'];
            } else {
                $user = 0;
            }

            // On prépare la requête
            $sql = 'INSERT INTO `historique` (`contact_id`,
                                              `compte_id`,
                                              `historique_type`,
                                              `historique_date`)
                    VALUES (' . $evenement . ',
                            ' . $user . ',
                            "autre",
                            NOW())';
            $query = $this->_link->prepare($sql);

            // On exécute la requête
            $query->execute();

            // On récupère l'identifiant de l'événement créé
            $identifiant = $this->_link->lastInsertId();

            // On effectue une recherche des informations
            $sql = 'SELECT *
                    FROM `historique`
                    WHERE `historique_id` = :evenement';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':evenement', $identifiant);
            $query->execute();
            $evenements = $query->fetchAll();
            $evenement = $evenements[0];

            // On commence par retraiter la date de l'événement
            $evenement['historique_date_fr'] = date(
                'd/m/Y',
                strtotime($evenement['historique_date'])
            );

            // On retraite ensuite l'ID pour l'avoir au format MD5
            $evenement['historique_md5'] = md5($evenement['historique_id']);

            // On retraite ensuite le type en clair
            $evenement['historique_type_clair'] = Core::eventType(
                $evenement['historique_type']
            );

            // On effectue une recherche des fichiers associés à l'événement
            $sql = 'SELECT *
                    FROM `fichiers`
                    WHERE `interaction_id` = :evenement';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':evenement', $identifiant);
            $query->execute();
            $fichiers = $query->fetchAll();

            // On modifie la liste des fichiers pour la formater en JSON
            $fichiers = json_encode($fichiers);

            // On ajoute la liste des fichiers associés à la liste des données
            $evenement['fichiers'] = $fichiers;

            // On effectue une recherche des tâches associées à l'événement
            $sql = 'SELECT *
                    FROM `taches`
                    WHERE `historique_id` = :evenement
                    AND `tache_terminee` = 0';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':evenement', $identifiant);
            $query->execute();
            $taches = $query->fetchAll();

            // On modifie la liste des tâches pour la formater en JSON
            $taches = json_encode($taches);

            // On ajoute la liste des tâches associées à la liste des données
            $evenement['taches'] = $taches;

            // On cherche les données sur le dossier, si un dossier est lié
            if ($evenement['dossier_id'] > 0) {
                    $sql = 'SELECT *
                            FROM `dossiers`
                            WHERE `dossier_id` = :id';
                    $query = $this->_link->prepare($sql);
                    $query->bindParam(':id', $evenement['dossier_id']);
                    $query->execute();
                    $dossier = $query->fetch(PDO::FETCH_ASSOC);
                    $dossier['dossier_md5'] = md5($dossier['dossier_id']);
                    $dossier = json_encode($dossier);

                    // On ajoute les informations sur le dossier
                    $evenement['dossier'] = $dossier;
            }

            // On retourne le tout dans la propriété privée evenement
            $this->_evenement = $evenement;
        } else {
            if ($securite === true) {
                $sql = 'SELECT *
                        FROM `historique`
                        WHERE MD5(`historique_id`) = :evenement';
            } else {
                $sql = 'SELECT *
                        FROM `historique`
                        WHERE `historique_id` = :evenement';
            }
            $query = $this->_link->prepare($sql);
            $query->bindParam(':evenement', $evenement);
            $query->execute();
            $evenements = $query->fetchAll();

            // Si on ne trouve pas d'utilisateur,
            // on retourne vers la page d'accueil du module contact
            if (!count($evenements)) {
                Core::goPage('contacts', true);
            } else {
                // On commence par retraiter la date de l'événement
                // pour l'avoir en format compréhensible
                $evenement = $evenements[0];
                $evenement['historique_date_fr'] = date(
                    'd/m/Y',
                    strtotime($evenement['historique_date'])
                );

                // On retraite ensuite l'ID pour l'avoir au format MD5
                $evenement['historique_md5'] = md5($evenement['historique_id']);

                // On retraite ensuite le type en clair
                $evenement['historique_type_clair'] = Core::eventType(
                    $evenement['historique_type']
                );

                // On effectue une recherche des fichiers associés à l'événement
                $sql = 'SELECT *
                        FROM `fichiers`
                        WHERE `interaction_id` = :evenement';
                $query = $this->_link->prepare($sql);
                $query->bindParam(':evenement', $evenement['historique_id']);
                $query->execute();
                $fichiers = $query->fetchAll();

                // On modifie la liste des fichiers pour la formater en JSON
                $fichiers = json_encode($fichiers);

                // On ajoute la liste des fichiers associés à la liste des données
                $evenement['fichiers'] = $fichiers;

                // On effectue une recherche des tâches associées à l'événement
                $sql = 'SELECT *
                        FROM `taches`
                        WHERE `historique_id` = :evenement
                        AND `tache_terminee` = 0';
                $query = $this->_link->prepare($sql);
                $query->bindParam(':evenement', $evenement['historique_id']);
                $query->execute();
                $taches = $query->fetchAll();

                // On modifie la liste des tâches pour la formater en JSON
                $taches = json_encode($taches);

                // On ajoute la liste des tâches associées à la liste des données
                $evenement['taches'] = $taches;

                 // On cherche les données sur le dossier, si un dossier est lié
                if ($evenement['dossier_id'] > 0) {
                    $sql = 'SELECT *
                            FROM `dossiers`
                            WHERE `dossier_id` = :id';
                    $query = $this->_link->prepare($sql);
                    $query->bindParam(':id', $evenement['dossier_id']);
                    $query->execute();
                    $dossier = $query->fetch(PDO::FETCH_ASSOC);
                    $dossier['dossier_md5'] = md5($dossier['dossier_id']);
                    $dossier = json_encode($dossier);

                    // On ajoute les informations sur le dossier
                    $evenement['dossier'] = $dossier;
                }

                // On retourne le tout dans la propriété evenement
                $this->_evenement = $evenement;
            }
        }
    }

    /**
     * Export informations in JSON
     *
     * @return string
     */
    public function json()
    {
        /**
         * Render inner function
         *
         * @param string $string string to decode
         *
         * @return string
         */
        function rendu($string)
        {
            return html_entity_decode($string, ENT_QUOTES);
        }

        $event = array_map("rendu", $this->_evenement);
        return json_encode($event);
    }

    /**
     * Is an event cliclable ?
     *
     * @return boolean
     */
    public function lien()
    {
        // On détermine la liste des types possédant une fiche détaillée
        $ouvert = ['contact', 'telephone', 'email', 'courrier', 'autre', 'rappel'];
        $campagne = ['sms', 'email', 'publi'];

        // On regarde si l'événement fait l'objet d'une fiche événement
        if (in_array($this->getInfos('type'), $ouvert)) {
            return 2;
        } elseif (in_array($this->getInfos('type'), $campagne)) {
            return 1;
        } else {
            return false;
        }
    }

    /**
     * Get an information
     *
     * @param string $infos information name
     *
     * @return mixed
     */
    public function getInfos(string $infos)
    {
        return $this->_evenement['historique_' . $infos];
    }

    /**
     * Get an information w/o prefix
     *
     * @param string $infos information name
     *
     * @return mixed
     */
    public function get(string $infos)
    {
        return $this->_evenement[$infos];
    }

    /**
     * Update an information
     *
     * @param string $info  information to update
     * @param string $value new value
     *
     * @return boolean
     */
    public function modification(string $info, string $value)
    {
        if ($info == 'historique_date') {
            $value = explode('/', $value);
            $value = $value[2] . '-' . $value[1] . '-' . $value[0];
        } else {
            $value = Core::securisationString($value);
        }

        $sql = 'UPDATE `historique`
                SET `'. $info . '` = "' . $value . '"
                WHERE `historique_id` = ' . $this->_evenement['historique_id'];
        $query = $this->_link->prepare($sql);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Event deletion
     *
     * @return void
     */
    public function suppression()
    {
        $identifiant = $this->_evenement['historique_id'];

        // On prépare la requête
        $sql = 'DELETE FROM `historique`
                WHERE `historique_id` = :event'
        $query = $this->_link->prepare($sql);

        // On affecte les informations discriminantes à la requête
        $query->bindParam(':event', $identifiant, PDO::PARAM_INT);

        // On lance la requête
        $query->execute();

        $sql = 'DELETE FROM `fichiers`
                WHERE `interaction_id` = :event';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':event', $identifiant);
        $query->execute();

        // On prépare la suppression des tâches liées à cet événement
        $sql = 'DELETE FROM `taches`
                WHERE `historique_id` = :event';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':event', $identifiant);
        $query->execute();
    }

    /**
     * Add a task
     *
     * @param integer $user     user
     * @param string  $task     task
     * @param string  $deadline deadline
     *
     * @return array
     */
    public function addTask(int $user, string $task, string $deadline)
    {
        // On retraite la deadline
        if (!empty($deadline)) {
            $deadline = explode('/', $deadline);
            krsort($deadline);
            $deadline = implode('-', $deadline);
        } else {
            $deadline = '0000-00-00';
        }

        // On récupère l'ID de l'utilisateur
        $compte = (isset($_COOKIE['leqg-user'])) ? $_COOKIE['leqg-user'] : 0;

        // On prépare la requête
        $sql = 'INSERT INTO `taches` (`createur_id`,
                                      `compte_id`,
                                      `historique_id`,
                                      `tache_description`,
                                      `tache_deadline`)
                VALUES (:createur,
                        :compte,
                        :evenement,
                        :description,
                        :deadline)';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':createur', $compte);
        $query->bindParam(':compte', $user);
        $query->bindParam(':evenement', $this->_evenement['historique_id']);
        $query->bindParam(':description', $task);
        $query->bindParam(':deadline', $deadline);

        // On exécute la variable
        $query->execute();

        // On récupère l'identifiant
        $tache = $this->_link->lastInsertId();

        $sql = 'SELECT *
                FROM `taches`
                WHERE `tache_id` = :tache';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':tache', $tache);
        $query->execute();
        $tache = $query->fetchAll();

        return $tache;
    }

    /**
     * Delete a task
     *
     * @param integer $task task to delete
     *
     * @return void
     */
    public function deleteTask(int $task)
    {
        // On prépare la requête
        $sql = 'UPDATE `taches`
                SET `tache_terminee` = 1
                WHERE `tache_id` = :tache';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':tache', $task);

        // On exécute la requête
        $query->execute();
    }

    /**
     * Link a folder to this event
     *
     * @param integer $dossier folder to link
     *
     * @return void
     */
    public function linkFolder(int $dossier)
    {
        // On modifie l'information
        $this->modification('dossier_id', $dossier);
    }

    /**
     * List all last interactions
     *
     * @param integer $nombre number
     *
     * @return array
     * @static
     */
    static public function last($nombre = 15)
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');

        // On prépare la requête
        $sql = 'SELECT `historique_id`
                FROM `historique`
                WHERE (
                    `historique_type` = "contact"
                    OR `historique_type` = "telephone"
                    OR `historique_type` = "courriel"
                    OR `historique_type` = "courrier"
                    OR `historique_type` = "autre"
                )
                ORDER BY `historique_date` DESC
                LIMIT 0, ' . $nombre;
        $query = $link->prepare($sql);
        $query->execute();

        // On fait la liste des dernières interactions en question
        $interactions = $query->fetchAll(PDO::FETCH_ASSOC);

        // On renvoit le tableau
        return $interactions;
    }

    /**
     * List all next tasks
     *
     * @param integer $nombre number
     *
     * @return array
     */
    static public function taches($nombre = 5)
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');

        // On prépare la requête
        $sql = 'SELECT `tache_id`,
                       `compte_id`,
                       `historique_id`,
                       `tache_description`,
                       `tache_deadline`
                FROM `taches`
                WHERE `tache_terminee` = 0
                ORDER BY `tache_deadline` DESC
                LIMIT 0,' . $nombre;
        $query = $link->prepare($sql);
        $query->execute();

        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * List all next tasks, for the active user
     *
     * @return array
     */
    public static function userTasks()
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');
        $userId = User::ID();

        // On prépare la requête
        $sql = 'SELECT `tache_id`,
                       `compte_id`,
                       `historique_id`,
                       `tache_description`,
                       `tache_deadline`
                FROM `taches`
                WHERE `tache_terminee` = 0
                AND `compte_id` = :compte
                ORDER BY `tache_deadline` DESC';
        $query = $link->prepare($sql);
        $query->bindParam(':compte', $userId, PDO::PARAM_INT);
        $query->execute();

        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }
}
