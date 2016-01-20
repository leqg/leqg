<?php
/**
 * Méthodes de traitement des campagnes d'envois
 *
 * PHP version 5
 *
 * @category Campagne
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Méthodes de traitement des campagnes d'envois
 *
 * PHP version 5
 *
 * @category Campagne
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Campagne
{
    /**
     * Propriété contenant le lien vers la BDD de l'utilisateur
     * @var PDO
     */
    private $_link;

    /**
     * Propriété contenant le lien vers la BDD générale
     * @var PDO
     */
    private $_core;

    /**
     * Tableau contenant les informations sur la campagne
     * @var array
     */
    private $_campagne = [];

    /**
     * Constructeur de cette classe
     *
     * @param string $campagne ID de la campagne (MD5)
     *
     * @return void
     */
    public function __construct(string $campagne)
    {
        // On récupère les informations sur la campagne demandée
        $query = Core::query('campagne-par-id');
        $query->bindParam(':campagne', $campagne, PDO::PARAM_INT);
        $query->execute();

        // On récupère les informations
        self::$_campagne = $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une information
     *
     * @param string $info Information demandée
     *
     * @return string
     */
    public function get(string $info)
    {
        return $this->_campagne[$info];
    }

    /**
     * Récupère des informations sur les contacts concernés
     *
     * @return array
     */
    public function contacts()
    {
        // On liste les éléments d'historique concernés
        $query = 'SELECT * FROM `historique` WHERE `campagne_id` = :id';
        $query = $this->_link->prepare($query);
        $query->bindParam(':id', $this->get('campagne_id'), PDO::PARAM_INT);
        $query->execute();
        $historique = $query->fetchAll(PDO::FETCH_ASSOC);

        // On fait le tableau des différents contacts concernés
        $contacts = [];
        foreach ($historique as $h) {
            $contacts[] = $h['contact_id'];
        }

        // On retraite le tableau des ids
        $ids = implode(',', $contacts);

        // On cherche tous les contacts concernés par l'envoi
        $query = 'SELECT *,
                         MD5(`contact_id`) AS `contact_md5`
                  FROM `contacts`
                  WHERE `contact_id` IN (' . $ids . ')';
        $query = $this->_link->query($query);

        if ($query->rowCount()) {
            $contacts = $query->fetchAll(PDO::FETCH_ASSOC);

            // Pour chaque contact, on rajoute le nom de la ville
            // et on traite le nom d'affichage
            foreach ($contacts as $key => $contact) {
                if ($contact['adresse_id']) {
                    $ville = Carto::villeParImmeuble($contact['adresse_id']);
                } elseif ($contact['immeuble_id']) {
                    $ville = Carto::villeParImmeuble($contact['immeuble_id']);
                } else {
                    $ville = 'Ville inconnue';
                }

                $contacts[$key]['ville'] = $ville;

                $contacts[$key]['nom_affichage'] = mb_convert_case(
                    $contact['contact_nom'],
                    MB_CASE_UPPER
                ) . ' ' . mb_convert_case(
                    $contact['contact_nom_usage'],
                    MB_CASE_UPPER
                ) . ' ' . mb_convert_case(
                    $contact['contact_prenoms'],
                    MB_CASE_TITLE
                );
            }
        } else {
            $contacts = array();
        }
        return $contacts;
    }

    /**
     * Création d'une campagne vierge
     *
     * @param string $type Type de campagne
     *
     * @return integer
     * @static
     */
    public static function nouvelle(string $type)
    {
        // On récupère le lien vers la base de données
        $link = Configuration::read('db.link');

        // On récupère l'identifiant de l'utilisateur connecté
        $user = User::ID();

        // On effectue la création de la campagne
        $query = Core::query('campagne-creation');
        $query->bindParam(':type', $type);
        $query->bindParam(':user', $user, PDO::PARAM_INT);
        $query->execute();

        // On retourne l'identifiant de la campagne
        return $link->lastInsertId();
    }

    /**
     * Créé une nouvelle campagne
     *
     * @param string $type  Type de campagne à créer
     * @param array  $infos Informations de création
     *
     * @return void
     * @static
     */
    public static function creation(string $type, array $infos)
    {
        // On récupère le lien vers la base de données
        $link = Configuration::read('db.link');

        // On récupère certaines informations
        $createur = User::ID();

        // On effectue la création des informations
        $query = 'INSERT INTO `campagne` (`campagne_type`,
                                          `campagne_titre`,
                                          `campagne_message`,
                                          `campagne_date`,
                                          `campagne_createur`)
                  VALUES (:type,
                          :titre,
                          :message,
                          NOW(),
                          :createur)';
        $query = $link->prepare($query);
        $query->bindParam(':type', $type);
        $query->bindParam(':titre', $infos['titre']);
        $query->bindParam(':message', $infos['message']);
        $query->bindParam(':createur', $createur, PDO::PARAM_INT);
        $query->execute();

        // On retourne l'identifiant de la campagne
        return $link->lastInsertId();
    }

    /**
     * Liste les campagnes existantes
     *
     * @param string $type Type de campagne à créer
     *
     * @return void
     * @static
     */
    public static function liste(string $type)
    {
        // On récupère le lien vers la base de données
        $link = Configuration::read('db.link');

        // On cherche la liste des campagnes du type demandé
        $query = 'SELECT MD5(`campagne_id`) AS `code`
                  FROM `campagne`
                  WHERE `campagne_type` = :type
                  ORDER BY `campagne_date` DESC';
        $query = $link->prepare($query);
        $query->bindParam(':type', $type);
        $query->execute();

        // On retourne la liste
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Créé un nouvel envoi
     *
     * @param string $objet   Objet de l'envoi
     * @param string $message Message de l'envoi
     * @param string $type    Type de l'envoi
     *
     * @return integer
     * @static
     */
    public static function envoi(string $objet, string $message, string $type)
    {
        // On récupère les données
        $user = User::ID();

        // On lance la création de l'envoi
        $link = Configuration::read('db.link');
        $query = 'INSERT INTO `envois` (`compte_id`,
                                        `envoi_type`,
                                        `envoi_time`,
                                        `envoi_titre`,
                                        `envoi_texte`)
                  VALUES (:compte,
                          :type,
                          NOW(),
                          :titre,
                          :texte)';
        $query = $link->prepare($query);
        $query->bindValue(':compte', $user, PDO::PARAM_INT);
        $query->bindValue(':type', $type);
        $query->bindValue(':titre', $objet);
        $query->bindValue(':texte', $message);
        $query->execute();

        return $link->lastInsertId();
    }
}
