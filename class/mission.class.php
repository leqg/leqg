<?php
/**
 * Mission class
 *
 * PHP version 5
 *
 * @category Mission
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Mission class
 *
 * PHP version 5
 *
 * @category Mission
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Mission
{
    /**
     * Mission data
     * @var array
     */
    private $_data = [];

    /**
     * Database PDO link
     * @var object
     */
    private $_link;

    /**
     * Class errors
     * @var boolean
     */
    public $err;

    /**
     * Last error message
     * @var string
     */
    public $err_msg;

    /**
     * Constructor method
     *
     * @param string $id Mission ID (md5 hash)
     *
     * @return void
     */
    public function __construct(string $id)
    {
        // On récupère la connexion à la base de données
        $this->_link = Configuration::read('db.link');

        // On cherche à récupérer les informations liées à cette mission
        $sql = 'SELECT *,
                       MD5(`mission_id`) AS `mission_hash`
                FROM `mission`
                WHERE MD5(`mission_id`) = :mission';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $id);
        $query->execute();

        if (!$query->rowCount()) {
            $this->err = true;
            $this->err_msg = 'Mission inexistante';
        }

        // On récupère les informations
        $mission = $query->fetch(PDO::FETCH_ASSOC);

        // On stocke les informations dans la classe
        $this->_data = $mission;
    }

    /**
     * Get an information
     *
     * @param string $information [description]
     *
     * @return mixed
     */
    public function get(string $information)
    {
        return $this->_data[ $information ];
    }

    /**
     * Update an information
     *
     * @param string $information information to update
     * @param string $value       new value
     *
     * @return boolean
     */
    public function set(string $information, string $value)
    {
        $mission = $this->get('mission_id');
        $sql = 'UPDATE `mission`
                SET `' . $information . '` = :valeur
                WHERE `mission_id` = :mission';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':valeur', $value);
        $query->bindParam(':mission', $mission);

        // On retourne un résultat selon la réussite ou non de l'opération
        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * User stats
     *
     * @return array
     */
    public function userStats()
    {
        // On récupère les variables
        $mission = $this->get('mission_id');

        $sql = 'SELECT *
                FROM `inscriptions`
                WHERE `mission_id` = :mission';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $mission, PDO::PARAM_INT);
        $query->execute();

        // On vérifie qu'il existe des militants
        if ($query->rowCount()) {
            // On récupère la liste de ces militants
            $militants = $query->fetchAll(PDO::FETCH_ASSOC);

            // On prépare le tableau des statistiques
            $statut = array(
                -1 => 'refus',
                 0 => 'invitation',
                 1 => 'inscrit'
            );

            // On lance une boucle de calcul du statut des militants
            $stats = array(
                'refus' => 0,
                'invitation' => 0,
                'inscrit' => 0
            );

            foreach ($militants as $militant) {
                $stats[$statut[$militant['inscription_statut']]]++;
            }

            // On cherche la liste de tous les reportings pour récupérer
            // les statistiques par militants
            $sql = 'SELECT `item_reporting_user`
                    FROM `items`
                    WHERE `mission_id` = :mission
                    AND `item_statut` != 0';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':mission', $mission, PDO::PARAM_INT);
            $query->execute();

            // On regarde s'il existe du reporting
            if ($query->rowCount()) {
                // On enregistre le nombre de reportings
                $stats['reporting'] = $query->rowCount();
                $stats['militants'] = array();

                // On fait une boucle de tous les reportings
                // pour compter l'activité par utilisateur
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $reporting) {
                    $user = $reporting['item_reporting_user'];
                    if (isset($stats['militants'][$user])) {
                        // On incrémente le nombre de reportings du militant
                        $stats['militants'][$user]++;
                    } else {
                        // On initialise le nombre de reportings du militant
                        $stats['militants'][$user] = 1;
                    }
                }

                // On tri le tableau des statistiques selon leur participation
                arsort($stats['militants']);

                // On récupère le militant le plus actif
                $militants_actifs = array_keys($stats['militants']);
                $stats['actif'] = $militants_actifs[0];
            } else {
                $stats['reporting'] = 0;
            }

            // On retourne les informations récupérées
            return $stats;
        } else {
            return false;
        }
    }

    /**
     * Members of a mission
     *
     * @param string $statut user status
     *
     * @return array
     */
    public function missionMembers($statut = null)
    {
        // On récupère la liste des inscrits pour le statut demandé
        $mission = $this->get('mission_id');
        if (is_null($statut)) {
            $sql = 'SELECT `user_id`
                    FROM `inscriptions`
                    WHERE `mission_id` = :mission';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':mission', $mission, PDO::PARAM_INT);
            $query->execute();

            // On vérifie qu'il existe des inscrits
            if ($query->rowCount()) {
                // On retourne le tableau
                $users = $query->fetchAll(PDO::FETCH_ASSOC);
                $militants = array();

                foreach ($users as $user) {
                    $militants[] = $user['user_id'];
                }

                return $militants;
            } else {
                return false;
            }
        } else {
            $sql = 'SELECT `user_id`
                    FROM `inscriptions`
                    WHERE `inscription_statut` = :statut
                    AND `mission_id` = :mission';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':statut', $statut);
            $query->bindParam(':mission', $mission, PDO::PARAM_INT);
            $query->execute();

            // On vérifie qu'il existe des inscrits
            if ($query->rowCount()) {
                // On retourne le tableau
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        }
    }

    /**
     * New user invitation
     *
     * @param integer $user user id
     *
     * @return boolean
     */
    public function newMember(int $user)
    {
        $mission = $this->get('mission_id');
        $sql = 'INSERT INTO `inscriptions` (`mission_id`, `user_id`)
                VALUES (:mission, :user)';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':user', $user);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * User response to an invitation
     *
     * @param integer $reponse User response
     * @param integer $user    User id
     *
     * @return boolean
     */
    public function reponse(int $reponse, int $user)
    {
        $mission = $this->get('mission_id');
        $sql = 'UPDATE `inscriptions`
                SET `inscription_statut` = :reponse
                WHERE MD5(`user_id`) = :user
                AND `mission_id` = :mission';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':reponse', $reponse);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':user', $user);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get stats about a mission
     *
     * @return array
     */
    public function missionStats()
    {
        // On récupère l'identifiant de la mission
        $mission = $this->get('mission_id');

        // On récupère tous les items à visiter dans la rue
        $sql = 'SELECT *
                FROM `items`
                WHERE `mission_id` = :mission';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $mission);
        $query->execute();

        if ($query->rowCount()) {
            $items = $query->fetchAll(PDO::FETCH_ASSOC);
            $stats = array(
                'attente' => 0,
                'absent' => 0,
                'ouvert' => 0,
                'procuration' => 0,
                'contact' => 0,
                'npai' => 0
            );

            foreach ($items as $item) {
                // On regarde si ce n'est pas encore fait
                if ($item['item_statut'] == 0) {
                    $stats['attente']++;
                } else {
                    if ($item['item_statut'] == 1) {
                        $stats['absent']++;
                    }
                    if ($item['item_statut'] == 2) {
                        $stats['ouvert']++;
                    }
                    if ($item['item_statut'] == 3) {
                        $stats['procuration']++;
                    }
                    if ($item['item_statut'] == 4) {
                        $stats['contact']++;
                    }
                    if ($item['item_statut'] == -1) {
                        $stats['npai']++;
                    }
                }
            }

            // On calcule la réalisation
            $stats['total'] = array_sum($stats);
            $stats['fait'] = $stats['total'] - $stats['attente'];
            $stats['proportion'] = ceil($stats['fait'] * 100 / $stats['total']);

            return $stats;
        } else {
            return false;
        }
    }

    /**
     * List all streets
     *
     * @return array
     */
    public function rues()
    {
        $mission = $this->get('mission_id');

        // On effectue une récupération de toutes les rues de la mission
        $sql = 'SELECT DISTINCT `rue_id`
                FROM `items`
                WHERE `mission_id` = :mission';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $mission);
        $query->execute();

        // On vérifie s'il existe des rues à parcourir
        if ($query->rowCount()) {
            $rues = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rues as $key => $rue) {
                $sql = 'SELECT *
                        FROM `street`
                        WHERE `id` = :rue';
                $query = $this->_link->prepare($sql);
                $query->bindParam(':rue', $rue['rue_id']);
                $query->execute();
                $street = $query->fetch(PDO::FETCH_ASSOC);

                $rues[$key] = $street;
            }

            return $rues;
        } else {
            return false;
        }
    }

    /**
     * Street stats
     *
     * @param integer $id street id
     *
     * @return array
     */
    public function streetStats(int $id)
    {
        // On récupère l'identifiant de la mission
        $mission = $this->get('mission_id');

        // On récupère tous les items à visiter dans la rue
        $sql = 'SELECT *
                FROM `items`
                WHERE `mission_id` = :mission
                AND `rue_id` = :rue';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':rue', $id);
        $query->execute();

        if ($query->rowCount()) {
            $items = $query->fetchAll(PDO::FETCH_ASSOC);
            $stats = array(
                'fait' => 0,
                'attente' => 0
            );

            foreach ($items as $item) {
                // On regarde si ce n'est pas encore fait
                if ($item['item_statut'] == 0) {
                    $stats['attente']++;
                } else {
                    $stats['fait']++;
                }
            }
            // On calcule la réalisation
            $stats['total'] = array_sum($stats);
            $stats['proportion'] = ceil($stats['fait'] * 100 / $stats['total']);

            return $stats;
        } else {
            return false;
        }
    }

    /**
     * Add a street in this mission
     *
     * @param integer $rue street id
     *
     * @return boolean
     */
    public function ajoutRue(int $rue)
    {
        // On effectue une recherche de tous les immeubles contenus
        $query = Core::query('building-by-street');
        $query->bindValue(':street', $rue, PDO::PARAM_INT);
        $query->execute();

        // S'il y a des immeubles
        if ($query->rowCount()) {
            // On récupère la liste des identifiants
            $immeubles = $query->fetchAll(PDO::FETCH_NUM);

            // Si la mission est un porte à porte,
            // on cherche les électeurs concernés
            if ($this->_data['mission_type'] == 'porte') {
                // Pour chaque immeuble, on recherche tous les électeurs
                foreach ($immeubles as $immeuble) {
                    $query = Core::query('people-by-building');
                    $query->bindValue(':building', $immeuble[0], PDO::PARAM_INT);
                    $query->execute();
                    $contacts = $query->fetchAll(PDO::FETCH_NUM);

                    // Pour chaque électeur, on créé une porte à frapper
                    foreach ($contacts as $contact) {
                        $query = Core::query('item-new');
                        $query->bindParam(
                            ':mission',
                            $this->_data['mission_id'],
                            PDO::PARAM_INT
                        );
                        $query->bindParam(':rue', $rue, PDO::PARAM_INT);
                        $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
                        $query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
                        $query->execute();
                    }
                }
            } else {
                foreach ($immeubles as $immeuble) {
                    $query = Core::query('item-boitage-new');
                    $query->bindParam(
                        ':mission', 
                        $this->_data['mission_id'], 
                        PDO::PARAM_INT
                    );
                    $query->bindParam(':rue', $rue, PDO::PARAM_INT);
                    $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
                    $query->execute();
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Add a ballot office in this mission
     *
     * @param integer $bureau Ballot office id
     *
     * @return boolean
     */
    public function ajoutBureau(int $bureau)
    {
        // On effectue une recherche de tous les immeubles contenus
        // dans le buerau demandée
        $sql = 'SELECT DISTINCT `id`
                FROM `people`
                WHERE `bureau` = :bureau';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':bureau', $bureau, PDO::PARAM_INT);
        $query->execute();

        // S'il y a des immeubles
        if ($query->rowCount()) {
            // On récupère la liste des identifiants
            $immeubles = $query->fetchAll(PDO::FETCH_NUM);

            // Si la mission est un porte à porte,
            // on cherche les électeurs concernés
            if ($this->_data['mission_type'] == 'porte') {
                // Pour chaque immeuble, on récupère les infos et
                // on créé une porte à boîter
                foreach ($immeubles as $immeuble) {
                    $sql = 'SELECT `street`
                            FROM `building`
                            WHERE `id` = :id';
                    $query = $this->_link->prepare($sql);
                    $query->bindParam(':id', $immeuble[0]);
                    $query->execute();
                    $info = $query->fetch(PDO::FETCH_NUM);

                    $sql = 'SELECT `people` AS `id`
                            FROM `address`
                            WHERE `building` = :immeuble';
                    $query = $this->_link->prepare($sql);
                    $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
                    $query->execute();
                    $contacts = $query->fetchAll(PDO::FETCH_NUM);

                    // Pour chaque électeur, on créé une porte à frapper
                    foreach ($contacts as $contact) {
                        $sql = 'INSERT INTO `items` (`mission_id`,
                                                     `rue_id`,
                                                     `immeuble_id`,
                                                     `contact_id`)
                                VALUES (:mission,
                                        :rue,
                                        :immeuble,
                                        :contact)';
                        $query = $this->_link->prepare($sql);
                        $query->bindParam(
                            ':mission',
                            $this->_data['mission_id'],
                            PDO::PARAM_INT
                        );
                        $query->bindParam(':rue', $info[0], PDO::PARAM_INT);
                        $query->bindParam(
                            ':immeuble',
                            $immeuble[0],
                            PDO::PARAM_INT
                        );
                        $query->bindParam(
                            ':contact',
                            $contact[0],
                            PDO::PARAM_INT
                        );
                        $query->execute();
                    }
                }
            } else {
                foreach ($immeubles as $immeuble) {
                    $sql = 'SELECT `street`
                            FROM `building`
                            WHERE `id` = :id';
                    $query = $this->_link->prepare($sql);
                    $query->bindParam(':id', $immeuble[0]);
                    $query->execute();
                    $info = $query->fetch(PDO::FETCH_NUM);

                    $query = $this->_link->prepare($sql);
                    $sql = 'INSERT INTO `items` (`mission_id`,
                                                 `rue_id`,
                                                 `immeuble_id`)
                            VALUES (:mission,
                                    :rue,
                                    :immeuble)';
                    $query->bindParam(
                        ':mission',
                        $this->_data['mission_id'],
                        PDO::PARAM_INT
                    );
                    $query->bindParam(':rue', $info[0], PDO::PARAM_INT);
                    $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
                    $query->execute();
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Report an action
     *
     * @param integer $electeur contact id
     * @param integer $statut   action status
     *
     * @return void
     */
    public function reporting(int $electeur, int $statut)
    {
        // On récupère les informations sur la mission
        $userId = User::ID();
        $mission = $this->_data;
        $type = $mission['mission_type'];

        // On prépare et on exécute déjà l'enregistrement du reporting
        if ($type == 'porte') {
            $sql = 'UPDATE `items`
                    SET `item_statut` = :statut,
                        `item_reporting_date` = NOW(),
                        `item_reporting_user` = :militant
                    WHERE `mission_id` = :mission
                    AND `contact_id` = :element';
            $query = $this->_link->prepare($sql);
        } elseif ($type == 'boitage') {
            $sql = 'UPDATE `items`
                    SET `item_statut` = :statut,
                        `item_reporting_date` = NOW(),
                        `item_reporting_user` = :militant
                    WHERE `mission_id` = :mission
                    AND `immeuble_id` = :element';
            $query = $this->_link->prepare($sql);
        }
        $query->bindParam(':statut', $statut);
        $query->bindParam(':militant', $userId, PDO::PARAM_INT);
        $query->bindParam(':mission', $mission['mission_id']);
        $query->bindParam(':element', $electeur);
        $query->execute();

        // S'il s'agit un porte à porte, on ajoute un événement pour le contact
        if ($type == 'porte') {
            // On prépare l'objet de l'historique
            $type_historique = array(
                'porte'   => 'Porte à porte',
                'boitage' => 'Boîtage'
            );

            $event_historique = array(
                1 => 'Électeur absent',
                2 => 'Électeur rencontré',
                3 => 'Demande de procuration',
                4 => 'Électeur à contacter',
                -1 => 'Adresse incorrecte'
            );

            $objet_historique = $type_historique[$type] .
                                ' – ' .
                                $event_historique[$statut];

            // On rajoute une entrée d'historique pour le contact en question
            $sql = 'INSERT INTO `historique` (`contact_id`,
                                              `compte_id`,
                                              `historique_type`,
                                              `historique_date`,
                                              `historique_objet`,
                                              `campagne_id`)
                    VALUES (:contact,
                            :compte,
                            "porte",
                            NOW(),
                            :objet,
                            :campagne)';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':contact', $electeur, PDO::PARAM_INT);
            $query->bindParam(':compte', $userId, PDO::PARAM_INT);
            $query->bindParam(':objet', $objet_historique);
            $query->bindParam(
                ':campagne',
                $mission['mission_id'],
                PDO::PARAM_INT
            );
            $query->execute();
        } elseif ($type == 'boitage' && $statut == 2) {
            // s'il s'agit d'un boîtage et que l'immeuble a été fait,
            // on fait un élément d'historique pour tous les habitants électeurs
            // déclarés dans l'immeuble concerné
            // On cherche tous les contacts qui habitent ou sont déclarés
            // électoralement dans l'immeuble en question pour créer un élément
            // d'historique
            $sql = 'SELECT `people`
                    FROM `address`
                    WHERE `building` = :immeuble';
            $query = $this->_link->prepare($sql);
            $query->bindParam(':immeuble', $electeur);
            $query->execute();
            $contacts = $query->fetchAll(PDO::FETCH_NUM);

            // On fait la boucle de tous ces contacts
            foreach ($contacts as $contact) {
                $sql = 'INSERT INTO `historique` (`contact_id`,
                                                  `compte_id`,
                                                  `historique_type`,
                                                  `historique_date`,
                                                  `historique_objet`,
                                                  `campagne_id`)
                        VALUES (:contact,
                                :compte,
                                "boite",
                                NOW(),
                                :mission,
                                :campagne)';
                $query = $this->_link->prepare($sql);
                $query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
                $query->bindParam(':compte', $userId, PDO::PARAM_INT);
                $query->bindParam(':mission', $this->_data['mission_nom']);
                $query->bindParam(
                    ':campagne',
                    $mission['mission_id'],
                    PDO::PARAM_INT
                );
                $query->execute();
            }
        }
    }

    /**
     * Number of procurations
     *
     * @return integer
     */
    public function procurationsNumber()
    {
        // On calcule le nombre de procurations demandées
        $sql = 'SELECT `item_id`
                FROM `items`
                WHERE `mission_id` = :mission
                AND `item_statut` = 3';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $this->_data['mission_id'], PDO::PARAM_INT);
        $query->execute();

        // On récupère le nombre demandé
        return $query->rowCount();
    }

    /**
     * Number of new contact asked
     *
     * @return integer
     */
    public function newContactsNumber()
    {
        // On calcule le nombre de recontacs demandés
        $sql = 'SELECT `item_id`
                FROM `items`
                WHERE `mission_id` = :mission
                AND `item_statut` = 4';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $this->_data['mission_id'], PDO::PARAM_INT);
        $query->execute();

        // On récupère le nombre demandé
        return $query->rowCount();
    }

    /**
     * List contacts
     *
     * @param integer $statut contact status
     *
     * @return array
     */
    public function contactsList(int $statut)
    {
        // On réalise le tri
        $sql = 'SELECT `contact_id`
                FROM `items`
                WHERE `mission_id` = :mission
                AND `item_statut` = :statut
                ORDER BY `item_reporting_date` DESC';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $this->_data['mission_id'], PDO::PARAM_INT);
        $query->bindParam(':statut', $statut, PDO::PARAM_INT);
        $query->execute();

        // On retourne le tableau des identifiants
        return $query->fetchAll(PDO::FETCH_NUM);
    }

    /**
     * All items to see in a street, by buildings
     *
     * @param integer $rue street id
     *
     * @return array|boolean
     */
    public function items(int $rue)
    {
        // On récupère l'identifiant de la mission
        $mission = $this->get('mission_id');

        // On récupère la liste des items
        $sql = 'SELECT `item_id`,
                       `immeuble_id`,
                       `contact_id`
                FROM `items`
                WHERE `mission_id` = :mission
                AND `rue_id` = :rue
                AND `item_statut` = 0';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':rue', $rue);
        $query->execute();

        // S'il existe des items à visiter
        if ($query->rowCount()) {
            $items = $query->fetchAll(PDO::FETCH_ASSOC);

            // S'il s'agit d'un porte à porte, on tri les informations
            if ($this->get('mission_type') == 'porte') {
                $immeubles = array();

                // On fabrique le tableau des immeubles
                foreach ($items as $item) {
                    $immeubles[$item['immeuble_id']][] = $item['contact_id'];
                }
                return $immeubles;
            } else {
                return $items;
            }
        } else {
            return false;
        }
    }

    /**
     * All items to see in a building
     *
     * @param integer $immeuble building id
     *
     * @return array|boolean
     */
    public function buildingItems(int $immeuble)
    {
        // On récupère l'identifiant de la mission
        $mission = $this->get('mission_id');

        // On récupère la liste des items
        $sql = 'SELECT `item_id`,
                       `immeuble_id`,
                       `contact_id`
                FROM `items`
                WHERE `mission_id` = :mission
                AND `immeuble_id` = :immeuble
                AND `item_statut` = 0';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':immeuble', $immeuble);
        $query->execute();

        // S'il existe des items à visiter
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * Count contacts by status for this mission
     *
     * @param integer $statut asked status
     *
     * @return array
     */
    public function contactsCount(int $statut)
    {
        // On réalise le tri
        $sql = 'SELECT `contact_id`
                FROM `porte`
                WHERE `mission_id` = :mission
                AND `porte_statut` = :statut
                ORDER BY `porte_date` DESC';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $this->_data['mission_id'], PDO::PARAM_INT);
        $query->bindParam(':statut', $statut, PDO::PARAM_INT);
        $query->execute();

        // On retourne le tableau des identifiants
        return $query->rowCount();
    }

    /**
     * Count buildings in a mission by status
     *
     * @param integer $statut asked status
     *
     * @return array
     */
    public function buidlingNumber(int $statut)
    {
        // On réalise le tri
        $sql = 'SELECT `boitage_id`
                FROM `boitage`
                WHERE `mission_id` = :mission
                AND `boitage_statut` = :statut
                ORDER BY `boitage_date` DESC';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $this->_data['mission_id'], PDO::PARAM_INT);
        $query->bindParam(':statut', $statut, PDO::PARAM_INT);
        $query->execute();

        // On retourne le tableau des identifiants
        return $query->rowCount();
    }

    /**
     * Open to user registration a mission
     *
     * @return boolean
     */
    public function ouvrir()
    {
        // On enregistre l'ouverture
        if ($this->set('mission_statut', 1)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Close to user registration a mission
     *
     * @return boolean
     */
    public function fermer()
    {
        // On enregistre la fermeture
        if ($this->set('mission_statut', 0)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Close an open mission
     *
     * @return void
     */
    public function cloture()
    {
        // On prépare la modification pour enregistrer la fermeture de la mission
        $sql = 'UPDATE `mission`
                SET `mission_statut` = 0
                WHERE `mission_id` = :mission';
        $query = $this->_link->prepare($sql);
        $query->bindParam(':mission', $this->_data['mission_id'], PDO::PARAM_INT);

        // On effectue la modification
        $query->execute();
    }

    /**
     * All openned mission list
     *
     * @param string $type mission type
     *
     * @return integer
     * @static
     */
    public static function missions(string $type)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des missions
        $sql = 'SELECT *
                FROM `mission`
                WHERE `mission_type` = "' . $type . '"
                ORDER BY `mission_deadline` ASC';
        $query = $link->query($sql);

        // On retourne la liste des missions s'il en existe
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return 0;
        }
    }

    /**
     * Create a new mission
     *
     * @param string $type  mission type
     * @param array  $infos mission informations
     *
     * @return integer
     * @static
     */
    public static function creation(string $type, array $infos )
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');
        $userId = User::ID();

        // On retraite la date entrée
        if (!empty($infos['date'])) {
            $date = explode('/', $infos['date']);
            krsort($date);
            $date = implode('-', $date);
        } else {
            $date = null;
        }

        // On exécute la requête d'insertion dans la base de données
        $sql = 'INSERT INTO `mission` (`createur_id`,
                                       `responsable_id`,
                                       `mission_deadline`,
                                       `mission_nom`,
                                       `mission_type`)
                VALUES (:cookie,
                        :responsable,
                        :deadline,
                        :nom,
                        :type)';
        $query = $link->prepare($sql);
        $query->bindParam(':cookie', $userId, PDO::PARAM_INT);
        $query->bindParam(':responsable', $infos['responsable'], PDO::PARAM_INT);
        $query->bindParam(':deadline', $date);
        $query->bindParam(':nom', $infos['nom']);
        $query->bindParam(':type', $type);
        $query->execute();

        // On affiche l'identifiant de la nouvelle mission
        return $link->lastInsertId();
    }

    /**
     * All user invitations
     *
     * @param string  $type mission type
     * @param integer $user user id
     *
     * @return array
     * @static
     */
    public static function invitations(string $type, int $user)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On récupère les invitations liées à l'utilisateur
        $sql = 'SELECT *
                FROM `inscriptions`
                WHERE `user_id` = :user
                AND `inscription_statut` = 0';
        $query = $link->prepare($sql);
        $query->bindParam(':user', $user);
        $query->execute();

        // On regarde s'il existe des invitations
        if ($query->rowCount()) {
            $missions = $query->fetchAll(PDO::FETCH_ASSOC);

            // On vérifie que les missions sont bien du bon type et ouvertes
            $missions_ouvertes = array();
            foreach ($missions as $mission) {
                $sql = 'SELECT *
                        FROM `mission`
                        WHERE `mission_type` = :type
                        AND `mission_statut` = 1
                        AND `mission_id` = :mission';
                $query = $link->prepare($sql);
                $query->bindParam(':type', $type);
                $query->bindParam(':mission', $mission['mission_id']);
                $query->execute();

                if ($query->rowCount()) {
                    $infos = $query->fetch(PDO::FETCH_ASSOC);
                    $missions_ouvertes[] = $infos['mission_id'];
                }
            }

            return $missions_ouvertes;
        } else {
            return false;
        }
    }

    /**
     * Open missions for an user
     *
     * @param string  $type mission type
     * @param integer $user user id
     *
     * @return array
     * @static
     */
    public static function openMissions(string $type, int $user)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On récupère les invitations liées à l'utilisateur
        $sql = 'SELECT *
                FROM `inscriptions`
                WHERE `user_id` = :user
                AND `inscription_statut` = 1';
        $query = $link->prepare($sql);
        $query->bindParam(':user', $user);
        $query->execute();

        // On regarde s'il existe des invitations
        if ($query->rowCount()) {
            $missions = $query->fetchAll(PDO::FETCH_ASSOC);

            // On vérifie que les missions sont bien du bon type et ouvertes
            $missions_ouvertes = array();
            foreach ($missions as $mission) {
                $sql = 'SELECT *
                        FROM `mission`
                        WHERE `mission_type` = :type
                        AND `mission_statut` = 1
                        AND `mission_id` = :mission';
                $query = $link->prepare($sql);
                $query->bindParam(':type', $type);
                $query->bindParam(':mission', $mission['mission_id']);
                $query->execute();

                if ($query->rowCount()) {
                    $infos = $query->fetch(PDO::FETCH_ASSOC);
                    $missions_ouvertes[] = $infos['mission_id'];
                }
            }
            return $missions_ouvertes;
        } else {
            return false;
        }
    }
}
