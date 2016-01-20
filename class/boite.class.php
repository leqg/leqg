<?php
/**
 * Méthodes de traitement des missions de boîtage
 *
 * PHP version 5
 *
 * @category Boitage
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Méthodes de traitement des missions de boîtage
 *
 * PHP version 5
 *
 * @category Boitage
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Boite
{
    /**
     * Propriété contenant le lien vers la base de données de l'utilisateur
     * @var object $_link
     */
    private $_link;

    /**
     * Constructeur de cette classe
     *
     * @return void
     */
    public function __construct()
    {
        $this->_link = Configuration::read('db.link');
    }

    /**
     * Vérification de l'inscription d'un membre à une mission
     *
     * @param int $mission ID de la mission
     *
     * @return bool
     * @static
     */
    static public function estInscrit(bool $mission)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');
        $userId = User::ID();

        // On exécute la requête de calcul du nombre de missions
        $query = 'SELECT *
                  FROM `inscriptions`
                  WHERE `mission_id` = :mission
                  AND `user_id` = :user';
        $query = $link->prepare($query);
        $query->bindParam(':mission', $mission, PDO::PARAM_INT);
        $query->bindParam(':user', $userId, PDO::PARAM_INT);
        $query->execute();

        // On affiche un booléen
        if ($query->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Calcul le nombre de missions disponibles
     *
     * @return integer
     * @static
     */
    static public function nombre()
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête
        $query = 'SELECT COUNT(*) AS `nombre`
                  FROM `mission`
                  WHERE `mission_statut` = 1
                  AND `mission_type` = "boitage"
                  AND (`mission_deadline` IS NULL
                       OR `mission_deadline` >= NOW()
                       OR `mission_deadline` = "0000-00-00")';
        $query = $link->query($query);
        $data = $query->fetch(PDO::FETCH_NUM);
        return $data[0];
    }

    /**
     * Retourne un tableau des missions disponibles actuellement
     *
     * @return array
     * @static
     */
    static public function missions()
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête
        $query = 'SELECT *
                  FROM `mission`
                  WHERE `mission_statut` = 1
                  AND `mission_type` = "boitage"
                  AND (`mission_deadline` IS NULL
                       OR `mission_deadline` >= NOW()
                       OR `mission_deadline` = "0000-00-00")';
        $query = $link->query($query);

        // On retourne le résultat
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Créer une nouvelle mission de boîtage
     *
     * @param array $infos Tableau contenant l'ensemble des informations
     *                     postées par l'utilisateur
     *
     * @return integer
     * @static
     */
    static public function creation(array $infos)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');
        $userID = User::ID();

        // On retraite la date entrée
        $date = explode('/', $infos['date']);
        krsort($date);
        $date = implode('-', $date);

        // On exécute la requête d'insertion dans la base de données
        $query = 'INSERT INTO `mission` (`createur_id`,
                                         `responsable_id`,
                                         `mission_deadline`,
                                         `mission_nom`,
                                         `mission_type`)
                  VALUES (:createur,
                          :responsable,
                          :deadline,
                          :nom,
                          "boitage")';
        $query = $link->prepare($query);
        $query->bindParam(':createur', $userID, PDO::PARAM_INT);
        $query->bindParam(':responsable', $infos['responsable'], PDO::PARAM_INT);
        $query->bindParam(':deadline', $date);
        $query->bindParam(':nom', $infos['nom']);
        $query->execute();

        // On retourne l'ID de la mission créée
        return $link->lastInsertId();
    }

    /**
     * Vérifie si une mission de boîtage correspond bien à l'ID renvoyé
     *
     * @param string $mission Entrée dont la véracité est à contrôler
     *
     * @return bool
     * @static
     */
    static public function verification(string $mission)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de vérification
        $query = 'SELECT `mission_id`
                  FROM `mission`
                  WHERE MD5(`mission_id`) = :id
                  AND `mission_type` = "boitage"';
        $query = $link->prepare($query);
        $query->bindParam(':id', $mission);
        $query->execute();

        // Si on trouve une entrée, c'est bon, sinon non
        if ($query->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Récupère toutes les informations concernant une mission de boîtage
     *
     * @param string $mission Identifiant de la mission pour laquelle
     *                        la récupération des informations est demandée
     *                        (chiffré MD5)
     *
     * @return array
     * @static
     */
    static public function informations(string $mission)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `mission`
                  WHERE MD5( `mission_id` ) = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $mission);
        $query->execute();

        // On retourne les informations sous forme d'un tableau
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne le nombre d'immeubles à réaliser dans une mission
     *
     * @param integer $mission Identifiant de la mission
     * @param string  $type    La recherche doit-elle porter sur
     *                         les immeubles fait (1), non-fait (0) ou tous (-1)
     *
     * @return integer
     * @static
     */
    static public function nombreImmeubles(int $mission, $type = 0)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête
        if ($type) {
            $query = 'SELECT COUNT(*)
                      AS `nombre`
                      FROM `boitage`
                      WHERE `mission_id` = :id
                      AND `boitage_statut` > 0';
        } else {
            $query = 'SELECT COUNT(*)
                      AS `nombre`
                      FROM `boitage`
                      WHERE `mission_id` = :id
                      AND `boitage_statut` = 0';
        }
        $query = $link->prepare($query);
        $query->bindParam(':id', $mission, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne le nombre d'immeubles trouvés
        return $data[0];
    }

    /**
     * Ajoute une rue entière dans une mission de boîtage
     *
     * @param integer $rue     ID de la rue à ajouter
     * @param integer $mission ID de la mission dans laquelle ajouter la rue
     *
     * @return boolean
     * @static
     */
    static public function ajoutRue(int $rue, int $mission)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On effectue une recherche de tous les immeubles contenus dans la rue
        $query = 'SELECT `immeuble_id`
                  FROM `immeubles`
                  WHERE `rue_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $rue, PDO::PARAM_INT);
        $query->execute();
        $immeubles = $query->fetchAll(PDO::FETCH_NUM);

        // Pour chaque immeuble, on créé une insertion dans la base de données
        foreach ($immeubles as $immeuble) {
            $query = 'INSERT INTO `boitage` (`mission_id`,
                                             `rue_id`,
                                             `immeuble_id`)
                      VALUES (:mission,
                              :rue,
                              :immeuble)';
            $query = $link->prepare($query);
            $query->bindParam(':mission', $mission, PDO::PARAM_INT);
            $query->bindParam(':rue', $rue, PDO::PARAM_INT);
            $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
            $query->execute();
        }
    }

    /**
     * Ajoute un bureau entier dans une mission de boîtage
     *
     * @param integer $bureau  ID du bureau à ajouter
     * @param integer $mission ID de la mission dans laquelle ajouter la rue
     *
     * @return boolean
     * @static
     */
    static public function ajoutBureau(int $bureau, int $mission)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On effectue une recherche de tous les immeubles contenus dans la rue
        $query = 'SELECT `immeuble_id`, `rue_id`
                  FROM `immeubles`
                  WHERE `bureau_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $bureau, PDO::PARAM_INT);
        $query->execute();
        $immeubles = $query->fetchAll(PDO::FETCH_NUM);

        // Pour chaque immeuble, on créé une insertion dans la base de données
        foreach ($immeubles as $immeuble) {
            $query = 'INSERT INTO `boitage` (`mission_id`, `rue_id`, `immeuble_id`)
                      VALUES (:mission, :rue, :immeuble)';
            $query = $link->prepare($query);
            $query->bindParam(':mission', $mission, PDO::PARAM_INT);
            $query->bindParam(':rue', $immeuble[1], PDO::PARAM_INT);
            $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
            $query->execute();
        }
    }

    /**
     * Obtenir une liste des immeubles à boiter par rue
     *
     * @param integer $mission ID de la mission
     * @param integer $statut  Statut des immeubles recherchés
     *                         (1 fait, 0 non fait)
     *
     * @return array
     * @static
     */
    static public function liste(int $mission, $statut = 0)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On récupère tous les immeubles
        if ($statut) {
            $query = 'SELECT `immeuble_id`, `rue_id`
                      FROM `boitage`
                      WHERE `mission_id` = :id
                      AND `boitage_statut` > 0';
        } else {
            $query = 'SELECT `immeuble_id`, `rue_id`
                      FROM `boitage`
                      WHERE `mission_id` = :id
                      AND `boitage_statut` = 0';
        }
        $query = $link->prepare($query);
        $query->bindParam(':id', $mission, PDO::PARAM_INT);
        $query->execute();
        $immeubles = $query->fetchAll(PDO::FETCH_NUM);

        // On lance un tri par rue des immeubles
        $rues = [];
        foreach ($immeubles as $immeuble) {
            $rues[$immeuble[1]][] = $immeuble[0];
        }

        // On retourne le tableau trié par rues
        return $rues;
    }

    /**
     * Estime le nombre d'électeur concernés par un boitage
     *
     * @param integer $mission ID de la mission concernée par l'estimation
     * @param integer $type    Type d'électeurs à vérifier
     *                         (1 : déjà boités, 0 : à boiter, 2 : tous)
     *
     * @return integer
     * @static
     */
    static public function estimation(int $mission, $type = 0)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des immeubles
        // concernés par le comptage
        if ($type) {
            $query = 'SELECT `immeuble_id`
                      FROM `boitage`
                      WHERE `mission_id` = :id
                      AND `boitage_statut` > 0';
        } else {
            $query = 'SELECT `immeuble_id`
                      FROM `boitage`
                      WHERE `mission_id` = :id
                      AND `boitage_statut` = 0';
        }
        $link->prepare($query);
        $query->bindParam(':id', $mission, PDO::PARAM_INT);
        $query->execute();
        $immeubles = $query->fetchAll(PDO::FETCH_NUM);

        // On retraite la liste des immeubles pour l'importer dans la requête SQL
        $ids = [];
        foreach ($immeubles as $immeuble) {
            $ids[] = $immeuble[0];
        }
        $immeubles = implode(',', $ids);

        if (count($ids)) {
            // On fait la recherche du nombre d'électeurs
            // pour tous les immeubles demandés
            $query = 'SELECT COUNT(*)
                      FROM `contacts`
                      WHERE `immeuble_id` IN (' . $immeubles . ')';
            $query = $link->query($query);

            if ($query->rowCount()) {
                $data = $query->fetch(PDO::FETCH_NUM);
                // On retourne le nombre d'électeurs
                return $data[0];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Effectue le reporting d'un boîtage
     *
     * @param string  $mission  ID de la mission concernée par le reporting (MD5)
     * @param string  $immeuble ID de l'immeuble concerné par le reporting (MD5)
     * @param integer $statut   Statut du reporting :
     *                          2 pour fait,
     *                          1 pour inaccessible
     *
     * @return void
     * @static
     */
    static public function reporting(string $mission, string $immeuble, int $statut)
    {
        // On met en place le lien vers la base de données
        $link = Configuration::read('db.link');

        // On récupère les informations sur la mission
        $informations = self::informations($mission);

        // On prépare et exécute la requête
        $query = 'UPDATE `boitage`
                  SET `boitage_statut` = :statut,
                      `boitage_date` = NOW(),
                      `boitage_militant` = :cookie
                  WHERE MD5(`mission_id`) = :mission
                  AND MD5(`immeuble_id`) = :immeuble'
        $query = $link->prepare($query);
        $query->bindParam(':statut', $statut);
        $query->bindParam(':cookie', User::ID(), PDO::PARAM_INT);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':immeuble', $immeuble);
        $query->execute();

        // Si l'immeuble a été fait,
        // on reporte le boitage pour tous les les contacts
        if ($statut == 2) {
            // On cherche tous les contacts qui habitent ou sont déclarés
            // électoralement dans l'immeuble en question pour
            // créer un élément d'historique
            $query = 'SELECT `contact_id`
                      FROM `contacts`
                      WHERE MD5(`immeuble_id`) = :immeuble
                      OR MD5(`adresse_id`) = :immeuble';
            $query = $link->prepare($query);
            $query->bindParam(':immeuble', $immeuble);
            $query->execute();
            $contacts = $query->fetchAll(PDO::FETCH_NUM);

            // On fait la boucle de tous ces contacts
            // pour leur ajouter l'élément d'historique
            foreach ($contacts as $contact) {
                $query = 'INSERT INTO `historique` (`contact_id`,
                                                    `compte_id`,
                                                    `historique_type`,
                                                    `historique_date`,
                                                    `historique_objet`)
                          VALUES (:contact,
                                  :compte,
                                  "boite",
                                  NOW(),
                                  :mission)';
                $query = $link->prepare($query);
                $query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
                $query->bindParam(':compte', User::ID(), PDO::PARAM_INT);
                $query->bindParam(':mission', $informations['mission_nom']);
                $query->execute();
            }
        }
    }
}
