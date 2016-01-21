<?php
/**
 * Phone calling missions system
 *
 * PHP version 5
 *
 * @category Rappel
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Phone calling missions system
 *
 * PHP version 5
 *
 * @category Rappel
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Rappel
{
    /**
     * Mission known data
     *
     * @var array
     */
    private $_mission = [];

    /**
     * PDO Database object
     *
     * @var object
     */
    private $_link;

    /**
     * Constructor
     *
     * @param integer $mission mission id
     *
     * @return void
     */
    public function __construct(int $mission)
    {
        // On commence par paramétrer les données PDO
        $this->_link = Configuration::read('db.link');

        // On cherche toutes les informations concernant la mission en cours
        // pour les ajouter à la propriété $mission
        $sql = 'SELECT *
                FROM argumentaires
                WHERE `argumentaire_id` = :id';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':id', $mission);

        // On exécute la recherche puis on l'affecte à la variable $mission
        $query->execute();
        $mission = $query->fetch(PDO::FETCH_ASSOC);

        // On recherche le nombre de numéros à appeler au sein de cette mission
        $sql = 'SELECT COUNT(*) AS `nombre`
                FROM `rappels`
                WHERE `argumentaire_id` = :argumentaire';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':argumentaire', $mission['argumentaire_id']);
        $query->execute();
        $rappels = $query->fetch(PDO::FETCH_ASSOC);
        $mission['nombre'] = $rappels['nombre'];

        // On recherche le nombre de numéros déjà appelés
        $sql = 'SELECT COUNT(*) AS `nombre`
                FROM `rappels`
                WHERE `argumentaire_id` = :argumentaire
                AND `rappel_statut` >= 2';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':argumentaire', $mission['argumentaire_id']);
        $query->execute();
        $rappels = $query->fetch(PDO::FETCH_ASSOC);
        $mission['fait'] = $rappels['nombre'];

        // On affecte le contenu de $mission à la propriété $mission
        $this->_mission = $mission;
    }

    /**
     * Get an information
     *
     * @param string $info name
     *
     * @return mixed
     */
    public function get(string $info)
    {
        return $this->_mission[$info];
    }

    /**
     * Update an information
     *
     * @param string $info   data name
     * @param string $valeur data value
     *
     * @return void
     */
    public function modification(string $info ,string $valeur )
    {
        // On prépare la requête de modification
        $sql = 'UPDATE `argumentaires`
                SET `' . $info . '` = :valeur
                WHERE `argumentaire_id` = :id';
        $query = $this->_link->prepare($sql);
        $query->bindParam(
            ':id',
            $this->_mission['argumentaire_id'],
            PDO::PARAM_INT
        );
        $query->bindParam(':valeur', $valeur);
        $query->execute();

        $this->_mission[ $info ] = $valeur;
    }

    /**
     * Get phonecall remarks
     *
     * @return array
     */
    public function commentaires()
    {
        // On recherche tous les appels de l'argumentaire avec reporting
        $sql = 'SELECT `contact_id`, `rappel_reporting`
                FROM `rappels`
                WHERE `rappel_statut` >= 2
                AND `argumentaire_id` = :argumentaire
                AND `rappel_reporting` != ""';
        $query = $this->_link->prepare($sql);
        $query->bindParam(
            ':argumentaire',
            $this->_mission['argumentaire_id'],
            PDO::PARAM_INT
        );
        $query->execute();

        // On regarde s'il existe des lignes à afficher
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }


    /**
     * Récupère les demandes de procurations liés aux appels de cet argumentaire
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @result Tableau des commentaires avec les informations connues
     */
    /**
     * Get all procurations asked
     *
     * @return array
     */
    public function procurations()
    {
        // On recherche tous les appels de l'argumentaire avec reporting
        $sql = 'SELECT `contact_id`, `rappel_reporting`
                FROM `rappels`
                WHERE `rappel_statut` = 3
                AND `argumentaire_id` = :argumentaire';
        $query = $this->_link->prepare($sql);
        $query->bindParam(
            ':argumentaire',
            $this->_mission['argumentaire_id'],
            PDO::PARAM_INT
        );
        $query->execute();

        // On regarde s'il existe des lignes à afficher
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * Get all new contact demands in a phonecall
     *
     * @return array
     */
    public function recontacts()
    {
        // On recherche tous les appels de l'argumentaire avec reporting
        $sql = 'SELECT `contact_id`, `rappel_reporting`
                FROM `rappels`
                WHERE `rappel_statut` = 4
                AND `argumentaire_id` = :argumentaire';
        $query = $this->_link->prepare($sql);
        $query->bindParam(
            ':argumentaire',
            $this->_mission['argumentaire_id'],
            PDO::PARAM_INT
        );
        $query->execute();

        // On regarde s'il existe des lignes à afficher
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * List all phonecall missions
     *
     * @param integer $statut mission status
     *
     * @return array
     * @static
     */
    public static function liste($statut = 1)
    {
        // On commence par paramétrer les données PDO
        $link = Configuration::read('db.link');

        // On prépare la requête
        $sql = 'SELECT `argumentaire_id`
                FROM `argumentaires`
                WHERE `argumentaire_statut` = :statut
                ORDER BY `argumentaire_deadline` ASC,
                         `argumentaire_creation` DESC';
        $query = $link->prepare($sql);
        $query->bindParam(':statut', $statut);

        // On exécute la requête et on récupère les données
        $query->execute();
        $missions = $query->fetchAll(PDO::FETCH_ASSOC);

        // On retourne le tableau des différentes missions
        return $missions;
    }

    /**
     * Create a new phonecall mission
     *
     * @return integer
     * @static
     */
    public static function creer()
    {
        // On commence par paramétrer les données PDO
        $link = Configuration::read('db.link');
        $userId = User::ID();

        // On prépare la requête
        $sql = 'INSERT INTO `argumentaires` (`createur_id`)
                VALUES (:id)';
        $query = $link->prepare($sql);
        $query->bindParam(':id', $userId);

        // On exécute la requête
        $query->execute();

        // On récupère l'identifiant des données insérées
        $identifiant = $link->lastInsertId();

        // On retourne cet identifiant
        return $identifiant;
    }

    /**
     * Number of phonecall to do in a mission
     *
     * @param array $args sorting methods
     *
     * @return integer
     * @static
     */
    public static function estimation(array $args)
    {
        // On commence par paramétrer les données PDO
        $link = Configuration::read('db.link');

        // On prépare la requête de récupération de tous les numéros
        // connus par le système
        $sql = 'SELECT *
                FROM `coordonnees`
                WHERE `coordonnee_type` != "email"';
        $query = $link->prepare($sql);
        $query->execute();
        $numeros = $query->fetchAll(PDO::FETCH_ASSOC);

        // On prépare le tableau des résultats du tri $num
        $num = array();

        // On retraite les arguments entrés
        if (isset($args['age']) && !empty($args['age'])) {
            $age = array();
            $age['usr'] = explode(':', $args['age']);
            $age['min'] = $age['usr'][0];
            $age['max'] = $age['usr'][1];
        } else {
            $age = false;
        }

        if (isset($args['bureaux']) && !empty($args['bureaux'])) {
            $bureaux = explode(',', $args['bureaux']);
        } else {
            $bureaux = false;
        }

        if (isset($args['thema']) && !empty($args['thema'])) {
            $thema = $args['thema'];
        } else {
            $thema = false;
        }

        // On fait la boucle de tous les numéros,
        // et on charge les informations pour chacun
        foreach ($numeros as $numero) {
            // On réinitialise le test
            $test = true;

            // On ouvre la fiche correspondante
            $contact = new People($numero['contact_id']);

            // On récupère son bureau de vote, son âge et ses tags
            $ageContact = $contact->age();
            $bureau = $contact->get('bureau');
            $tags = $contact->get('tags');

            // On vérifie si la fiche correspond aux arguments entrés
            if ($age && $test) {
                if ($ageContact <= $age['max'] && $ageContact >= $age['min']) {
                    // On le rajoute au test
                    $test = true;
                    $testAge = true;
                } else {
                    $test = false;
                    $testAge = false;
                }
            }

            // On fait les vérifications concernant le bureau de vote
            if ($bureaux && $test) {
                if (in_array($bureau, $bureaux)) {
                    $test = true;
                    $testBureau = true;
                } else {
                    $test = false;
                    $testBureau = false;
                }
            }

            // On fait les vérifications concernant les thématiques
            if ($thema && $test) {
                if (in_array($thema, $tags)) {
                    $test = true;
                } else {
                    $test = false;
                }
            }

            // Si le test est concluant, on l'ajoute à la liste des numéros
            // en utilisant comme key l'ID du contact pour éviter les doublons
            if ($test) {
                $id = $contact->get('id');
                $num[ $id ] = $numero['coordonnee_id'];
            }
        }

        // On calcule maintenant le nombre de numéros concluant
        $nombre = count($num);

        // On retourne cette estimation
        return $nombre;
    }
}
