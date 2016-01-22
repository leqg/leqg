<?php
/**
 * Door to door missions
 *
 * PHP version 5
 *
 * @category Porte
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Door to door missions
 *
 * PHP version 5
 *
 * @category Porte
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Porte
{
    /**
     * Count all missions
     *
     * @return integer
     * @static
     */
    public static function nombre()
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de calcul du nombre de missions
        $sql = 'SELECT COUNT(*) AS nombre
                FROM `mission`
                WHERE `mission_statut` = 1
                AND `mission_type` = "porte"
                AND (`mission_deadline` IS NULL OR `mission_deadline` >= NOW())';
        $query = $link->query($sql);
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne le nombre retrouvé
        return $data[0];
    }

    /**
     * User subscription status
     *
     * @param integer $mission Mission ID
     *
     * @return boolean
     * @static
     */
    public static function estInscrit(int $mission)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');
        $userId = User::ID();

        // On exécute la requête de calcul du nombre de missions
        $sql = 'SELECT * FROM `inscriptions`
                WHERE `mission_id` = :mission
                AND `user_id` = :user';
        $query = $link->prepare($sql);
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
     * Get all missions
     *
     * @return integer
     * @static
     */
    public static function missions()
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des missions
        $sql = 'SELECT *
                FROM `mission`
                WHERE `mission_statut` = 1
                AND `mission_type` = "porte"
                AND (`mission_deadline` IS NULL OR `mission_deadline` >= NOW())
                ORDER BY `mission_deadline` ASC';
        $query = $link->query($sql);

        // On retourne le tableau récupéré
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new mission
     *
     * @param array $infos new mission args
     *
     * @return integer
     * @static
     */
    public static function creation(array $infos)
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
                        "porte")';
        $query = $link->prepare($sql);
        $query->bindParam(':cookie', $userId, PDO::PARAM_INT);
        $query->bindParam(':responsable', $infos['responsable'], PDO::PARAM_INT);
        $query->bindParam(':deadline', $date);
        $query->bindParam(':nom', $infos['nom']);
        $query->execute();

        // On affiche l'identifiant de la nouvelle mission
        return $link->lastInsertId();
    }

    /**
     * Verify an ID
     *
     * @param string $mission mission ID to verify (md5 hash)
     *
     * @return boolean
     * @static
     */
    public static function verification( $mission )
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de vérification
        $sql = 'SELECT `mission_id`
                FROM `mission`
                WHERE MD5( `mission_id` ) = :id
                AND `mission_type` = "porte"';
        $query = $link->prepare($sql);
        $query->bindParam(':id', $mission);
        $query->execute();

        return ($query->rowCount()) ? true : false;
    }

    /**
     * Door to door mission informations
     *
     * @param string $mission Mission ID (md5 hash)
     *
     * @return array
     * @static
     */
    public static function informations($mission)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $sql = 'SELECT *
                FROM `mission`
                WHERE MD5(`mission_id`) = :id';
        $query = $link->prepare($sql);
        $query->bindParam(':id', $mission);
        $query->execute();

        // On retourne les informations sous forme d'un tableau
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count the number of voters
     *
     * @param integer $mission mission ID
     * @param integer $type    type of voters
     *
     * @return integer
     * @static
     */
    public static function nombreVisites( $mission , $type = 0 )
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête
        if ($type) {
            $sql = 'SELECT COUNT(*)
                    FROM `porte`
                    WHERE `mission_id` = :mission
                    AND `porte_statut` > 0';
        } else {
            $sql = 'SELECT COUNT(*)
                    FROM `porte`
                    WHERE `mission_id` = :mission
                    AND `porte_statut` = 0';
        }
        $query = $link->prepare($sql);
        $query->bindParam(':mission', $mission, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne le nombre de visites trouvés
        return $data[0];
    }

    /**
     * Add a street in the mission
     *
     * @param integer $rue     Street id
     * @param integer $mission Mission id
     *
     * @return boolean
     * @static
     */
    public static function ajoutRue(int $rue, int $mission)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On effectue une recherche de tous les immeubles contenus dans la rue
        $sql = 'SELECT `immeuble_id`
                FROM `immeubles`
                WHERE `rue_id` = :id';
        $query = $link->prepare($sql);
        $query->bindParam(':id', $rue, PDO::PARAM_INT);
        $query->execute();
        $immeubles = $query->fetchAll(PDO::FETCH_NUM);

        // Pour chaque immeuble, on cherche tous les électeurs de l'immeuble
        foreach ($immeubles as $immeuble) {
            $sql = 'SELECT `contact_id`
                    FROM `contacts`
                    WHERE `immeuble_id` = :immeuble
                    OR `adresse_id` = :immeuble';
            $query = $link->prepare($sql);
            $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
            $query->execute();
            $contacts = $query->fetchAll(PDO::FETCH_NUM);

            // Pour chaque électeur, on créé une porte à frapper
            foreach ($contacts as $contact) {
                $sql = 'INSERT INTO `porte` (`mission_id`,
                                             `rue_id`,
                                             `immeuble_id`,
                                             `contact_id`)
                        VALUES (:mission,
                                :rue,
                                :immeuble,
                                :contact)';
                $query = $link->prepare($sql);
                $query->bindParam(':mission', $mission, PDO::PARAM_INT);
                $query->bindParam(':rue', $rue, PDO::PARAM_INT);
                $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
                $query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
                $query->execute();
            }
        }
    }

    /**
     * Add a ballot office in a mission
     *
     * @param integer $bureau  ballot office id
     * @param integer $mission mission id
     *
     * @return boolean
     */
    public static function ajoutBureau(int $bureau, int $mission)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On effectue une recherche de tous les immeubles contenus dans la rue
        $sql = 'SELECT `immeuble_id`, `rue_id`
                FROM `immeubles`
                WHERE `bureau_id` = :id';
        $query = $link->prepare($sql);
        $query->bindParam(':id', $bureau, PDO::PARAM_INT);
        $query->execute();
        $immeubles = $query->fetchAll(PDO::FETCH_NUM);

        // Pour chaque immeuble, on cherche tous les contacts pour ajouter pour
        // chacun une entrée dans la base porte à frapper
        foreach ($immeubles as $immeuble) {
            $sql = 'SELECT `contact_id`
                    FROM `contacts`
                    WHERE `immeuble_id` = :immeuble
                    OR `adresse_id` = :immeuble'
            $query = $link->prepare($sql);
            $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
            $query->execute();
            $contacts = $query->fetchAll(PDO::FETCH_NUM);

            // Pour chaque électeur, on créé une porte à frapper
            foreach ($contacts as $contact) {
                $sql = 'INSERT INTO `porte` (`mission_id`,
                                             `rue_id`,
                                             `immeuble_id`,
                                             `contact_id`)
                        VALUES (:mission,
                                :rue,
                                :immeuble,
                                :contact)';
                $query = $link->prepare($sql);
                $query->bindParam(':mission', $mission, PDO::PARAM_INT);
                $query->bindParam(':rue', $immeuble[1], PDO::PARAM_INT);
                $query->bindParam(':immeuble', $immeuble[0], PDO::PARAM_INT);
                $query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
                $query->execute();
            }
        }
    }

    /**
     * List all buildings by street
     *
     * @param integer $mission mission id
     * @param integer $statut  building status (1 done, 0 to do)
     *
     * @return array
     * @static
     */
    public static function liste(int $mission, $statut = 0)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On récupère la liste de toutes les portes
        $sql = 'SELECT `immeuble_id`, `rue_id`
                FROM `porte`
                WHERE `mission_id` = :id';
        $query = $link->prepare($sql);
        $query->bindParam(':id', $mission, PDO::PARAM_INT);
        $query->execute();
        $portes = $query->fetchAll(PDO::FETCH_NUM);

        // On lance le tri par immeuble des portes à frapper
        $immeubles = array();
        foreach ($portes as $porte) {
            if (!array_key_exists($porte[0], $immeubles)) {
                $immeubles[$porte[0]] = array(
                'immeuble_id' => $porte[0],
                'rue_id' => $porte[1]
                );
            }
        }

        // On lance le tri par rues des immeubles
        $rues = array();
        foreach ($immeubles as $immeuble) {
            $rues[$immeuble['rue_id']][] = $immeuble['immeuble_id'];
        }

        // On retourne le tableau trié
        return $rues;
    }


    /**
     * Load all voters by building
     *
     * @param string $mission  mission id (md5 hash)
     * @param string $immeuble building id (md5 hash)
     *
     * @return array
     * @static
     */
    public static function electeurs(string $mission, string $immeuble)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On récupère la liste des portes à frapper dans l'immeuble demandé
        $sql = 'SELECT *
                FROM `porte`
                WHERE MD5(`mission_id`) = :mission
                AND MD5(`immeuble_id`) = :immeuble
                AND `porte_statut` = 0';
        $query = $link->prepare($sql);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':immeuble', $immeuble);
        $query->execute();
        $portes = $query->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque porte, on cherche les informations du contact
        foreach ($portes as $key => $porte) {
            $sql = 'SELECT *
                    FROM `contacts`
                    WHERE `contact_id` = :contact';
            $query = $link->prepare($sql);
            $query->bindParam(':contact', $porte['contact_id']);
            $query->execute();
            $contact = $query->fetch(PDO::FETCH_ASSOC);

            $portes[$key] = array_merge($portes[$key], $contact);
        }

        // On trie le tableau des électeurs par nom
        Core::triMultidimentionnel($portes, 'contact_nom', SORT_ASC);

        // On retourne le tableau trié
        return $portes;
    }

    /**
     * Count the number of voters in a mission
     *
     * @param integer $mission mission id
     * @param integer $type    voter type (1 done, 0 to do, 2 all)
     *
     * @return integer
     * @static
     */
    public static function estimation(int $mission, $type = 0)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');

        // On prépare la requête de recherche des immeubles
        // concernés par le comptage
        if ($type) {
            $sql = 'SELECT COUNT(*)
                    FROM `porte`
                    WHERE `mission_id` = :mission
                    AND `porte_statut` > 0';
        } else {
            $sql = 'SELECT COUNT(*)
                    FROM `porte`
                    WHERE `mission_id` = :mission
                    AND `porte_statut` = 0';
        }
        $query = $link->prepare($sql);
        $query->bindParam(':mission', $mission, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne le nombre de portes cherchées
        return $data[0];
    }

    /**
     * Report a mission
     *
     * @param string  $mission  mission id (md5 hash)
     * @param string  $electeur voter id (md5 hash)
     * @param integer $statut   status (2 done, 1 inaccessible)
     *
     * @return void
     * @static
     */
    public static function reporting( $mission , $electeur , $statut )
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');
        $userId = User::ID();

        // On récupère les informations sur la mission
        $informations = self::informations($mission);

        $sql = 'UPDATE `porte`
                SET `porte_statut` = :statut,
                    `porte_date` = NOW(),
                    `porte_militant` = :militant
                WHERE MD5(`mission_id`) = :mission
                AND MD5(`contact_id`) = :contact';
        // On prépare et exécute la requête
        $query = $link->prepare($sql);
        $query->bindParam(':statut', $statut);
        $query->bindParam(':militant', $userId, PDO::PARAM_INT);
        $query->bindParam(':mission', $mission);
        $query->bindParam(':contact', $electeur);
        $query->execute();

        // On recherche l'identifiant en clair du contact vu
        $sql = 'SELECT `contact_id`
                FROM `contacts`
                WHERE MD5(`contact_id`) = :contact';
        $query = $link->prepare($sql);
        $query->bindParam(':contact', $electeur);
        $query->execute();
        $contact = $query->fetch(PDO::FETCH_NUM);

        // On rajoute une entrée d'historique pour le contact en question
        $sql = 'INSERT INTO `historique` (`contact_id`,
                                          `compte_id`,
                                          `historique_type`,
                                          `historique_date`,
                                          `historique_objet`)
                VALUES (:contact,
                        :compte,
                        "porte",
                        NOW(),
                        :nom)';
        $query = $link->prepare($sql);
        $query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
        $query->bindParam(':compte', $userId, PDO::PARAM_INT);
        $query->bindParam(':nom', $informations['mission_nom']);
        $query->execute();
    }

    /**
     * Get registration in this mission
     *
     * @param integer $mission mission id
     *
     * @return array
     * @static
     */
    public static function inscriptions(int $mission)
    {
        // On récupère la connexion à la base de données
        $link = Configuration::read('db.link');
        $userId = User::ID();

        $sql = 'SELECT *
                FROM `inscriptions`
                WHERE `mission_id` = :mission
                AND `user_id` = :user';
        $query = $link->prepare($sql);
        $query->bindParam(':mission', $mission, PDO::PARAM_INT);
        $query->bindParam('user', $userId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
