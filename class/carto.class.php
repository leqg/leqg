<?php
/**
 * Noyau cartographique
 *
 * PHP version 5
 *
 * @category Carto
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Noyau cartographique
 *
 * PHP version 5
 *
 * @category Carto
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Carto
{
    /**
     * Retourne une liste de toutes les villes répondant à la recherche lancée
     *
     * @param string $search Ville à recherche
     *
     * @return array
     * @static
     */
    static public function rechercheVille(string $search)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On sécurise la recherche
        $search = "%$search%";

        // On prépare le tableau de destination finale des résultats
        $villes = array();

        // On lance la requête de recherche approximative
        // (mais en excluant les correspondances exactes trouvées plus haut)
        $query = 'SELECT *
                  FROM `communes`
                  WHERE `commune_nom_propre` LIKE :search
                  ORDER BY `commune_nom` ASC
                  LIMIT 0, 25';
        $query = $link->prepare($query);
        $query->bindParam(':search', $search);
        $query->execute();

        // On retourne le tableau des résultats
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne une liste de toutes les rues répondant à la recherche lancée
     *
     * @param int    $ville  ID de la ville dans laquelle effectuer la recherche
     * @param string $search Rue à rechercher
     *
     * @return array
     * @static
     */
    static public function rechercheRue(int $ville, $search = '')
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On sécurise la recherche
        $search = '%'.$search.'%';

        // On vérifie que la ville entrée est bien un champ numérique
        if (!is_numeric($ville)) {
            return false;
        }

        // On lance la requête de recherche approximatinve
        // (mais en excluant les correspondances exactes trouvées plus haut
        $query = 'SELECT *, SHA2(`rue_id`, 256) AS `rue_code`
                  FROM `rues`
                  WHERE `commune_id` = :ville
                  AND `rue_nom` LIKE :search
                  ORDER BY `rue_nom` ASC LIMIT 0, 30';
        $query = $link->prepare($query);
        $query->bindParam(':ville', $ville, PDO::PARAM_INT);
        $query->bindParam(':search', $search, PDO::PARAM_STR);
        $query->execute();

        // On retourne le tableau des résultats
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne une liste de toutes les rues de la base répondant
     * à la recherche lancée au format JSON
     *
     * @param string $search Rue à rechercher, toutes villes confondues
     *
     * @return array
     * @static
     */
    static public function rechercheRueJSON(string $search)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On sécurise la recherche
        $search = '%'.$search.'%';

        // On exécute la requête de recherche
        $query = 'SELECT *
                  FROM `rues`
                  LEFT JOIN `communes`
                  ON `communes`.`commune_id` = `rues`.`commune_id`
                  WHERE `rue_nom` LIKE :search
                  ORDER BY `rue_nom` ASC
                  LIMIT 0, 30';
        $query = $link->prepare($query);
        $query->bindParam(':search', $search, PDO::PARAM_STR);
        $query->execute();

        // On récupère les résultats
        $rues = $query->fetchAll(PDO::FETCH_ASSOC);

        // On retourne les résultats au format JSON
        return json_encode($rues);
    }

    /**
     * Retourne une liste de toutes les villes de la base
     * répondant à la recherche lancée au format JSON
     *
     * @param string $search Ville à rechercher
     *
     * @return array
     * @static
     */
    static public function rechercheVilleJSON(string $search)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On sécurise la recherche
        $search = '%'.$search.'%';

        // On exécute la requête de recherche
        $query = 'SELECT *
                  FROM `communes`
                  WHERE `commune_nom_propre` LIKE :search
                  ORDER BY `commune_nom` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':search', $search, PDO::PARAM_STR);
        $query->execute();

        // On retourne le tableau des résultats sous format JSON
        $villes = $query->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($villes);
    }

    /**
     * Retourne une liste de toutes les bureaux de la base
     * répondant à la recherche lancée au format JSON
     *
     * @param string $search Bureau à rechercher, toutes villes confondues
     *
     * @return array
     * @static
     */
    static public function rechercheBureauJSON(string $search)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On sécurise la recherche
        $search = '%'.$search.'%';

        // On exécute la requête de recherche
        $query = 'SELECT *, SHA2(`bureau_id`, 256) AS `bureau_hash`
                  FROM `bureaux`
                  WHERE `bureau_code` LIKE :search
                  OR `bureau_nom` LIKE :search
                  ORDER BY `bureau_code`, `bureau_nom` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':search', $search, PDO::PARAM_STR);
        $query->execute();

        // On récupère la liste des bureaux correspondants à la recherche
        $bureaux = $query->fetchAll(PDO::FETCH_ASSOC);

        // On prépare la liste des informations connues sur les communes
        // pour éviter des recherches redondantes inutiles
        $communes_vues = array();
        $communes_donnees = array();

        // Pour chaque bureau, on cherche les informations sur la commune
        foreach ($bureaux as $key => $bureau) {
            // On vérifie d'abord si on possède déjà des informations
            if (in_array($bureau['commune_id'], $communes_vues)) {
                $bureaux[$key] = array_merge(
                    $bureau,
                    $communes_donnees[$bureau['commune_id']]
                );
            } else {
                $query = 'SELECT *
                          FROM `communes`
                          WHERE `commune_id` = :commune';
                $query = $link->prepare($query);
                $query->bindParam(
                    ':commune',
                    $bureau['commune_id'],
                    PDO::PARAM_INT
                );
                $query->execute();

                // On récupère les informations sur la commune du bureau de vote
                // pour les enregistrer en supprimant les informations
                // des anciennes recherches
                unset($infos);
                $infos = $query->fetch(PDO::FETCH_ASSOC);

                // On affecte ces informations aux tableaux pour les retrouver
                $communes_vues[] = $infos['commune_id'];
                $communes_donnees[$infos['commune_id']] = $infos;

                $bureaux[$key] = array_merge($bureau, $infos);
            }
        }

        // On encode les informations trouvées en JSON et on les retourne au script
        return json_encode($bureaux);
    }

    /**
     * Retourne une liste de tous les cantons répondant à la recherche lancée
     *
     * @param string $search Canton à rechercher
     *
     * @return array
     * @static
     */
    static public function rechercheCanton(string $search)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On sécurise la recherche
        $search = '%'.$search.'%';

        // On exécute la requête de recherche
        $query = 'SELECT *
                  FROM `cantons`
                  WHERE `canton_nom` LIKE :search
                  ORDER BY `canton_nom` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':search', $search, PDO::PARAM_INT);
        $query->execute();

        // On retourne le tableau
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à une région demandée
     *
     * @param integer $id ID de la région demandée
     *
     * @return array
     * @static
     */
    static public function region(int $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `regions`
                  WHERE `region_id` = :region';
        $query = $link->prepare($query);
        $query->bindParam(':region', $id, PDO::PARAM_INT);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à un département demandé
     *
     * @param integer $id ID du département demandé
     *
     * @return array
     * @static
     */
    static public function departement(int $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `departements`
                  WHERE `departement_id` = :departement';
        $query = $link->prepare($query);
        $query->bindParam(':departement', $id, PDO::PARAM_INT);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à un arrondissement demandé
     *
     * @param integer $id ID de l'arrondissement demandé
     *
     * @return array
     * @static
     */
    static public function arrondissement(int $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `arrondissements`
                  WHERE `arrondissement_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à un canton demandé
     *
     * @param int $id ID du canton demandé
     *
     * @return array
     * @static
     */
    static public function canton(int $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `cantons`
                  WHERE `canton_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à un bureau de vote demandé
     *
     * @param string $id ID du bureau de vote demandé
     *
     * @return array
     * @static
     */
    static public function bureauSecure(string $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `bureaux`
                  WHERE SHA2(`bureau_id`, 256) = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à un bureau de vote demandé
     *
     * @param int $id ID du bureau de vote demandé
     *
     * @return array
     * @static
     */
    static public function bureau(int $id)
    {
        return self::bureauSecure(hash('sha256', $id));
    }

    /**
     * Retourne les informations relatives à une ville demandée
     *
     * @param string $id ID de la ville demandée
     *
     * @return array
     * @static
     */
    static public function villeSecure(string $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `communes`
                  WHERE SHA2(`commune_id`, 256) = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $id);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à une ville demandée
     *
     * @param int $id ID de la ville demandée
     *
     * @return array
     * @static
     */
    static public function ville(int $id)
    {
        return self::villeSecure(hash('sha256', $id));
    }

    /**
     * Retourne les informations relatives à une rue demandée
     *
     * @param string $id ID de la rue demandée
     *
     * @return array
     * @static
     */
    static public function rueSecure(string $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `rues`
                  WHERE SHA2(`rue_id`, 256) = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à une rue demandée
     *
     * @param int $id ID de la rue demandée
     *
     * @return array
     * @static
     */
    static public function rue(int $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `rues`
                  WHERE `rue_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Retourne les informations relatives à un immeuble demandé
     *
     * @param string $id ID de l'immeuble demandé
     *
     * @return array
     * @static
     */
    static public function immeubleSecure(string $id)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de recherche des informations
        $query = 'SELECT *
                  FROM `immeubles`
                  WHERE SHA2(`immeuble_id`, 256) = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        // On retourne les résultats
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les informations relatives à un immeuble demandé
     *
     * @param int $id ID de l'immeuble demandé
     *
     * @return array
     * @static
     */
    static public function immeuble(int $id)
    {
        return self::immeubleSecure(hash('sha256', $id));
    }

    /**
     * Retourne le nom d'un arrondissement grâce à son ID
     *
     * @param integer $id     ID de l'arrondissement demandé
     * @param boolean $return Méthode de retour de l'information demandée
     *
     * @return string|void
     * @static
     */
    static public function afficherArrondissement(int $id, $return = false)
    {
        // On lance la recherche d'informations
        $arrondissement = self::arrondissement($id);

        // On retourne le résultat demandé
        if ($return) {
            return $arrondissement['nom'];
        } else {
            echo $arrondissement['nom'];
        }
    }

    /**
     * Retourne le nom d'un canton grâce à son ID
     *
     * @param integer $id     ID du canton demandé
     * @param boolean $return Méthode de retour de l'information demandée
     *
     * @return string|void
     * @static
     */
    static public function afficherCanton(int $id, $return = false)
    {
        // On lance la recherche d'informations
        $canton = self::canton($id);

        // On retourne le résultat demandé
        if ($return) {
            return $canton['nom'];
        } else {
            echo $canton['nom'];
        }
    }

    /**
     * Retourne le nom d'une ville grâce à son ID
     *
     * @param integer $id     ID de la ville demandée
     * @param boolean $return Méthode de retour de l'information demandée
     *
     * @return string|void
     * @static
     */
    static public function afficherVille(int $id, $return = false)
    {
        // On lance la recherche d'informations
        $ville = self::ville($id);

        // On retourne le résultat demandé
        if ($return) {
            return $ville['commune_nom'];
        } else {
            echo $ville['commune_nom'];
        }
    }


    /**
     * Retourne le nom d'une rue grâce à son ID
     *
     * @param integer $id     ID de la rue demandée
     * @param boolean $return Méthode de retour de l'information demandée
     *
     * @return string|void
     * @static
     */
    static public function afficherRue(int $id, $return = false)
    {
        // On lance la recherche d'informations
        $rue = self::rue($id);

        // On retourne le résultat demandé
        if ($return) :
            return $rue['rue_nom'];
        else :
            echo $rue['rue_nom'];
        endif;
    }

    /**
     * Retourne le nom d'un département grâce à son ID
     *
     * @param integer $id     ID du département demandé
     * @param boolean $return Méthode de retour de l'information demandée
     *
     * @return string|void
     * @static
     */
    static public function afficherDepartement(int $id, $return = false)
    {
        // On lance la recherche d'informations
        $rue = self::departement($id);

        // On retourne l'information
        if (!$return) {
            echo $data['departement_nom'];
        }

        return $data['departement_nom'];
    }

    /**
     * Retourne le nom d'une région grâce à son ID
     *
     * @param integer $id     ID de la région demandée
     * @param boolean $return Méthode de retour de l'information demandée
     *
     * @return string|void
     * @static
     */
    static public function afficherRegion(int $id, $return = false)
    {
        // On lance la recherche d'informations
        $rue = self::region($id);

        // On retourne l'information
        if (!$return) {
            echo $data['region_nom'];
        }

        return $data['region_nom'];
    }

    /**
     * Retourne le numéro d'un immeuble grâce à son ID
     *
     * @param integer $id     ID de l'immeuble demandé
     * @param boolean $return Méthode de retour de l'information demandée
     *
     * @return string|void
     * @static
     */
    static public function afficherImmeuble(int $id ,$return = false)
    {
        // On lance la recherche d'informations
        $immeuble = self::immeuble($id);

        // On retourne le résultat demandé
        if ($return) :
            return $immeuble['immeuble_numero'];
        else :
            echo $immeuble['immeuble_numero'];
        endif;
    }

    /**
     * Retourne une liste de bureaux de vote par ville
     *
     * @param integer $ville ID de la ville contenant les bureaux de vote demandés
     *
     * @return array
     * @static
     */
    static public function listeBureaux(int $ville)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des immeubles correspondant
        $query = 'SELECT *
                  FROM `bureaux`
                  WHERE `commune_id` = :ville
                  ORDER BY `bureau_code`, `bureau_nom` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':ville', $ville, PDO::PARAM_INT);
        $query->execute();

        // On retourne les données
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne la liste de tous les bureaux de vote connus
     *
     * @return array
     * @static
     */
    static public function listeTousBureaux()
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des immeubles correspondant
        $query = 'SELECT *
                  FROM `bureaux`
                  ORDER `bureau_code`,
                        `bureau_nom` ASC';
        $query = $link->prepare($query);
        $query->execute();

        // On retourne les résultats
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne une liste de rues par ville
     *
     * @param int $ville ID de la ville demandée
     *
     * @return array
     * @static
     */
    static public function listeRues(int $ville)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des immeubles correspondant
        $query = 'SELECT *
                  FROM `rues`
                  WHERE `commune_id` = :ville
                  ORDER BY `rue_nom` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':ville', $ville, PDO::PARAM_INT);
        $query->execute();

        // On retourne les données
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne une liste des immeubles dans une rue demandée
     *
     * @param integer $rue ID de la rue demandée
     *
     * @return array
     * @static
     */
    static public function listeImmeubles(int $rue)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des immeubles correspondant
        $query = 'SELECT *,
                         `immeuble_id` AS `id`,
                         `immeuble_numero` AS `numero`
                  FROM `immeubles`
                  WHERE `rue_id` = :rue
                  ORDER BY `immeuble_numero` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':rue', $rue, PDO::PARAM_INT);
        $query->execute();

        // On récupère le résultat
        $immeubles = $query->fetchAll(PDO::FETCH_ASSOC);

        // Pour le tri, on retire toutes les lettres de la colonne numéro
        foreach ($immeubles as $key => $immeuble) {
            // On enregistre le numéro en retirant tout ce qui n'est pas
            // un chiffre
            $immeubles[$key]['numero_safe'] = preg_replace(
                '#[^0-9]#',
                '',
                $immeuble['numero']
            );
        }

        // On trie le tableau pour des résultats dans un ordre logique
        Core::triMultidimentionnel($immeubles, 'numero_safe');

        // On retourne le tableau trié
        return $immeubles;
    }

    /**
     * Retourne une liste des électeurs d'un immeuble
     *
     * @param integer $immeuble ID de l'immeuble demandé
     *
     * @return array
     * @static
     */
    static public function listeElecteurs(int $immeuble)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des électeurs correspondant
        $query = 'SELECT `contact_nom`,
                         `contact_nom_usage`,
                         `contact_prenoms`,
                         `contact_sexe`,
                         `contact_organisme`,
                         `contact_email`,
                         `contact_fixe`,
                         `contact_mobile`,
                         MD5(`contact_id`) AS `code`
                  FROM `contacts`
                  WHERE (`immeuble_id` = :immeuble)
                  AND `contact_electeur` = 1
                  ORDER BY `contact_nom`,
                           `contact_nom_usage`,
                           `contact_prenoms` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':immeuble', $immeuble, PDO::PARAM_INT);
        $query->execute();

        // On retourne les données
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne le nombre d'électeurs dans un immeuble
     *
     * @param integer $immeuble ID de l'immeuble concerné par le comptage
     *
     * @return integer
     * @static
     */
    static public function nombreElecteursParImmeuble(int $immeuble)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des électeurs correspondant
        $query = 'SELECT COUNT(*) AS `nombre
                  FROM `contacts`
                  WHERE `immeuble_id` = :immeuble
                  AND `contact_electeur` = 1
                  ORDER BY `contact_nom`,
                           `contact_nom_usage`,
                           `contact_prenoms` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':immeuble', $immeuble, PDO::PARAM_INT);
        $query->execute();

        // On récupère le nombre d'électeurs
        $nombre = $query->fetch(PDO::FETCH_NUM);

        // On retourne le nombre d'électeur
        return $nombre[0];
    }

    /**
     * Retourne le nombre d'électeurs dans un bureau de vote
     *
     * @param integer $bureau      ID du bureau de vote concerné par le comptage
     * @param boolean $coordonnees Si true ne compte que les électeurs dont le
     *                             système  connait des coordonnées connait
     *                             des coordonnées
     *
     * @return integer
     * @static
     */
    static public function listeElecteursParBureau(
        int $bureau,
        $coordonnees = false
    ) {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête de récupération des électeurs correspondant
        if ($coordonnees) {
            $query = 'SELECT *,
                             MD5(`contact_id`) AS `code`
                      FROM `contacts`
                      WHERE `bureau_id` = :bureau
                      AND (
                            (contact_email > 0 AND contact_optout_email = 0)
                            OR (contact_fixe > 0 AND contact_optout_fixe = 0)
                            OR (contact_mobile > 0 AND contact_optout_mobile = 0)
                          )
                      AND contact_optout_global = 0
                      ORDER BY `contact_nom`,
                               `contact_nom_usage`,
                               `contact_prenoms` ASC';
        } else {
            $query = 'SELECT *,
                             MD5(`contact_id`) AS `code`
                      FROM `contacts`
                      WHERE `bureau_id` = :bureau
                      ORDER BY `contact_nom`,
                               `contact_nom_usage`,
                               `contact_prenoms` ASC';
        }
        $query = $link->prepare($query);
        $query->bindParam(':bureau', $bureau);
        $query->execute();

        // On retourne les données
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne le bureau de vote d'un immeuble demandé
     *
     * @param integer $immeuble ID de l'immeuble demandé
     *
     * @return integer
     * @static
     */
    static public function bureauParImmeuble(int $immeuble)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On cherche l'information dans la base de données
        $query = 'SELECT `bureau_id`
                  FROM `immeubles`
                  WHERE `immeuble_id` = :immeuble';
        $query = $link->prepare($query);
        $query->bindParam(':immeuble', $immeuble, PDO::PARAM_INT);
        $query->execute();

        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne l'ID du bureau
        return $data[0];
    }

    /**
     * Retourne la ville correspondante à un immeuble
     *
     * @param integer $immeuble ID de l'immeuble concerné par la demande
     *
     * @return integer
     * @static
     */
    static public function villeParImmeuble(int $immeuble)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On cherche l'information dans la base de données
        $query = 'SELECT `rue_id` FROM `immeuble` WHERE `immeuble_id` = :immeuble';
        $query = $link->prepare($query);
        $query->bindParam(':immeuble', $immeuble, PDO::PARAM_INT);
        $query->execute();
        $rue = $query->fetch(PDO::FETCH_NUM);

        // On cherche l'information concernant la ville
        $query = 'SELECT `commune_id` FROM `rues` WHERE `rue_id` = :rue';
        $query = $link->prepare($query);
        $query->bindParam(':rue', $rue[0], PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne l'ID de la ville
        return $data[0];
    }

    /**
     * Retourne la ville correspondante à une rue
     *
     * @param integer $rue ID de la rue concerné par la demande
     *
     * @return integer
     * @static
     */
    static public function villeParRue(int $rue)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On cherche l'information dans la base de données
        $query = 'SELECT `commune_id` FROM `rues` WHERE `rue_id` = :rue';
        $query = $link->prepare($query);
        $query->bindParam(':rue', $rue, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne l'id de la ville
        return $data[0];
    }

    /**
     * Retourne le département à partir d'une ville
     *
     * @param integer $ville ID de la ville concerné par la demande
     *
     * @return integer
     * @static
     */
    static public function departementParVille(int $ville)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On cherche l'information dans la base de données
        $query = 'SELECT `departement_id`
                  FROM `communes`
                  WHERE `commune_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $ville, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne l'id du département
        return $data[0];
    }

    /**
     * Retourne le canton correspondant à un immeuble
     *
     * @param integer $immeuble ID de l'immeuble concerné par la demande
     *
     * @return integer
     * @static
     */
    static public function cantonParImmeuble(int $immeuble)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On cherche l'information dans la base de données
        $query = 'SELECT `canton_id`
                  FROM `immeubles`
                  WHERE `immeuble_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $immeuble, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne l'id du canton
        return $data[0];
    }

    /**
     * Retourne un tableau contenant toutes les informations
     * géographiques disponibles pour un immeuble
     *
     * @param integer $immeuble ID de l'immeuble concerné par la demande
     *
     * @return array
     * @static
     */
    static public function detailAdresse(int $immeuble)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On récupère les données sur l'immeuble
        $query = 'SELECT * FROM `immeubles` WHERE `immeuble_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $immeuble, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);

        // On récupère les données sur la rue
        $query = 'SELECT * FROM `rues` WHERE `rue_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $data['rue_id'], PDO::PARAM_INT);
        $query->execute();
        $data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));

        // On récupère les données sur la ville
        $query = 'SELECT * FROM `communes` WHERE `commune_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $data['commune_id'], PDO::PARAM_INT);
        $query->execute();
        $data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));

        // On récupère les données sur le code postal
        $query = 'SELECT * FROM `codes_postaux` WHERE `commune_id` = :id';
        $query = $link->prepare();
        $query->bindParam(':id', $data['commune_id'], PDO::PARAM_INT);
        $query->execute();
        $data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));

        // On récupère les données sur le département
        $query = 'SELECT * FROM `departements` WHERE `departement_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $data['departement_id'], PDO::PARAM_INT);
        $query->execute();
        $data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));

        // On récupère les données sur la région
        $query = 'SELECT * FROM `regions` WHERE `region_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $data['region_id'], PDO::PARAM_INT);
        $query->execute();
        $data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));

        // On retourne le tableau complet
        return $data;
    }

    /**
     * Retourne une adresse postale complète à partir d'un immeuble demandé
     *
     * @param integer $immeuble   ID de l'immeuble concerné par la demande
     * @param string  $separateur Séparateur entre les composants de l'adresse
     * @param boolean $return     Si oui, retourne l'information plutôt
     *                            que de l'afficher
     *
     * @return string|void
     * @static
     */
    static public function adressePostale(
        int $immeuble,
        $separateur = '<br>',
        $return = false
    ) {
        // On récupère les informations liées à l'adresse de l'immeuble demandée
        $informations = self::detailAdresse($immeuble);

        // On formate les composants de l'adresse correctement
        $adresse['numero'] = $informations['immeuble_numero'];
        $adresse['rue'] = mb_convert_case(
            $informations['rue_nom'],
            MB_CASE_TITLE
        );
        $adresse['cp'] = $informations['code_postal'];
        $adresse['ville'] = mb_convert_case(
            $informations['commune_nom'],
            MB_CASE_UPPER
        );

        // On prépare la variable d'affichage du rendu
        $affichage = $adresse['numero'] . ' ';

        // On affiche conditionnement la suite de l'adresse
        if (!empty($adresse['rue'])) {
            $affichage .= $adresse['rue'] . $separateur;
        }
        if (!empty($adresse['cp'])) {
            $affichage .= $adresse['cp'] . ' ';
        }
        if (!empty($adresse['ville'])) {
            $affichage .= $adresse['ville'] . $separateur;
        }

        // On remet en forme l'affichage
        $affichage = Core::transformText($affichage);

        // On retourne les informations demandées
        if (!$return) {
            echo $affichage;
        }
        return $affichage;
    }

    /**
     * Retourne les informations sur le bureau de vote d'un immeuble
     *
     * @param integer $immeuble ID de l'immeuble concerné par la demande
     * @param boolean $return   Si oui, retourne l'information
     * @param boolean $mini     Si oui, prépare une version réduite
     *
     * @return string|void
     * @static
     */
    static public function bureauDeVote($immeuble, $return = false, $mini = false)
    {
        // On récupère toutes les informations nécessaires par rapport
        // à cet immeuble et donc son bureau de vote
        $informations = self::detailAdresse($immeuble);

        // On retraite les informations
        $bureau['numero'] = $informations['bureau_code'];
        $bureau['nom'] = mb_convert_case(
            $informations['bureau_nom'],
            MB_CASE_TITLE
        );
        $bureau['ville'] = mb_convert_case(
            $informations['commune_nom'],
            MB_CASE_UPPER
        );

        // On prépare le rendu
        if ($mini) {
            $affichage = 'Bureau ' . $bureau['numero'] . ' – ' . $bureau['nom'];
        } else {
            $affichage = 'Bureau ' . $bureau['numero'] . ' – ' . $bureau['ville'] .
                         '<br>' . $bureau['nom'];
        }

        // On affiche le rendu si demandé
        if (!$return) {
            echo $affichage;
        }

        // On retourne dans tous les cas le rendu
        return $affichage;
    }

    /**
     * Ajoute une nouvelle rue à la base de données
     *
     * @param integer $ville ID de la ville dans laquelle se trouve la rue
     * @param string  $rue   Nom de la rue à ajouter dans la base de données
     *
     * @return integer
     * @static
     */
    static public function ajoutRue(int $ville, string $rue)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On exécute la requête SQL
        $query = 'INSERT INTO `rues` (`commune_id`, `rue_nom`)
                  VALUES (:ville, :rue)';
        $query = $link->prepare($query);
        $query->bindParam(':ville', $ville, PDO::PARAM_INT);
        $query->bindParam(':rue', $rue, PDO::PARAM_STR);
        $query->execute();

        // On retourne l'identifiant des informations insérées
        return $link->lastInsertId();
    }

    /**
     * Ajoute un nouvel immeuble à une rue
     *
     * @param array $infos Informations relatives au nouvel immeuble
     *
     * @return integer
     * @static
     */
    static public function ajoutImmeuble(array $infos)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        if (isset($infos['rue'], $infos['numero'])) {
            // On exécute la requête
            $query = 'INSERT INTO `immeubles` (`rue_id`, `immeuble_numero`)
                      VALUES (:rue, :numero)';
            $query = $link->prepare($query);
            $query->bindParam(':rue', $infos['rue'], PDO::PARAM_INT);
            $query->bindParam(':numero', $infos['numero'], PDO::PARAM_STR);
            $query->execute();

            // On retourne le numéro de l'entrée insérée
            return $link->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * Estime le nombre d'électeur pour un découpage géographique demandé
     *
     * @param string  $branche     Découpage géographique concerné
     * @param integer $id          ID du découpage géographique concerné
     * @param string  $coordonnees Permet de restreindre l'estimation aux électeurs
     *                             dont certaines coordonnées sont connues
     *
     * @return integer
     */
    static public function nombreElecteurs($branche, int $id, $coordonnees = null)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On recherche tous les immeubles si la branche est un bureau
        if ($branche == 'bureau') {
            if (!is_null($coordonnees)) {
                $query = 'SELECT COUNT(*)
                          FROM `contacts`
                          WHERE ( contacts.contact_' . $coordonnees .
                                  ' > 0 AND contact_optout_' . $coordonnees .
                                  ' = 0 )
                          AND `bureau_id` = :id';
            } else {
                $query = 'SELECT COUNT(*)
                          FROM `contacts`
                          WHERE `contact_electeur` = 1
                          AND `bureau_id` = :id';
            }
            $query = $link->prepare($query);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_NUM);
            $nombre = $data[0];
        } else if ($branche == 'immeuble') {
            // On recherche le nombre de contacts, électeurs,
            // dans les immeubles en question
            if (!is_null($coordonnees)) {
                $query = 'SELECT COUNT(*)
                          FROM `contacts`
                          WHERE ( contacts.contact_' . $coordonnees .
                                  ' > 0 AND contact_optout_' . $coordonnees .
                                  ' = 0 )
                          AND (`immeuble_id` = :id OR `adresse_id` = :id)';
            } else {
                $query = 'SELECT COUNT(*)
                          FROM `contacts`
                          WHERE `contact_electeur` = 1
                          AND `immeuble_id` = :id';
            }
            $query = $link->prepare($query);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_NUM);
            $nombre = $data[0];
        } else if ($branche == 'rue') {
            // On recherche tous les immeubles de chaque rue de cette commune
            $query = 'SELECT `immeuble_id`
                      FROM `immeubles`
                      WHERE `rue_id` = :id';
            $query = $link->prepare($query);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount()) {
                $immeubles = $query->fetchAll(PDO::FETCH_NUM);

                // On formate la liste des rues pour l'insérer
                // dans la recherche SQL des électeurs
                $ids = array(); // Liste des ids des rues de la commune
                foreach ($immeubles as $immeuble) {
                    $ids[] = $immeuble[0];
                }
                $immeubles = implode(',', $ids);

                // On vérifie qu'il existe bien des immeubles,
                // sinon il n'y a pas d'électeur
                if ($query->rowCount()) {
                    // On recherche le nombre de contacts, électeurs,
                    // dans les immeubles en question
                    if (!is_null($coordonnees)) {
                        $query = 'SELECT COUNT(*)
                                  FROM `contacts`
                                  WHERE (
                                    contacts.contact_' . $coordonnees . ' > 0
                                    AND contact_optout_' . $coordonnees . ' = 0
                                  )
                                  AND (
                                    `immeuble_id` IN (' . $immeubles . ')
                                    OR `adresse_id` IN (' . $immeubles . ')
                                  )';
                    } else {
                        $query = 'SELECT COUNT(*)
                                  FROM `contacts`
                                  WHERE `contact_electeur` = 1
                                  AND `immeuble_id` IN (' . $immeubles . ')';
                    }
                    $query = $link->query($query);
                    $data = $query->fetch(PDO::FETCH_NUM);
                    $nombre = $data[0];
                } else {
                    $nombre = 0;
                }
            } else {
                $nombre = 0;
            }
        } else {
            // On exécute la requête de recherche de toutes les rues de la commune
            $query = 'SELECT `rue_id` FROM `rues` WHERE `commune_id` = :id';
            $query = $link->prepare($query);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $rues = $query->fetchAll(PDO::FETCH_NUM);

            // On formate la liste des rues pour l'insérer dans la recherche SQL
            // des immeubles
            $ids = array(); // Liste des ids des rues de la commune
            foreach ($rues as $rue) {
                $ids[] = $rue[0];
            }
            $rues = implode(',', $ids);

            // On vérifie qu'il existe bien des rues, sinon il n'y a pas d'électeur
            if ($query->rowCount()) {
                // On recherche tous les immeubles de chaque rue de cette commune
                $query = 'SELECT `immeuble_id`
                          FROM `immeubles`
                          WHERE `rue_id` IN (' . $rues . ')';
                $query = $link->query($query);
                $immeubles = $query->fetchAll(PDO::FETCH_NUM);

                // On formate la liste des rues pour l'insérer dans la recherche
                // SQL des électeurs
                $ids = array(); // Liste des ids des rues de la commune
                foreach ($immeubles as $immeuble) {
                    $ids[] = $immeuble[0];
                }
                $immeubles = implode(',', $ids);

                // On vérifie qu'il existe bien des immeubles,
                // sinon il n'y a pas d'électeur
                if ($query->rowCount()) {
                    // On recherche le nombre de contacts, électeurs,
                    // dans les immeubles en question
                    if (!is_null($coordonnees)) {
                        $query = 'SELECT COUNT(*)
                                  FROM `contacts`
                                  WHERE (
                                    contacts.contact_' . $coordonnees . ' > 0
                                    AND contact_optout_' . $coordonnees . ' = 0
                                  )
                                  AND (
                                    `immeuble_id` IN (' . $immeubles . ')
                                    OR `adresse_id` IN (' . $immeubles . ')
                                  )';
                    } else {
                        $query = 'SELECT COUNT(*)
                                  FROM `contacts`
                                  WHERE `contact_electeur` = 1
                                  AND `immeuble_id` IN (' . $immeubles . ')';
                    }
                    $query = $link->query($query);
                    $data = $query->fetch(PDO::FETCH_NUM);
                    $nombre = $data[0];
                } else {
                    $nombre = 0;
                }
            } else {
                $nombre = 0;
            }
        }

        // On retourne le résultat
        if ($nombre) {
            return $nombre;
        } else {
            return 0;
        }
    }

    /**
     * Retourne s'il existe dans un immeuble des fiches où des coordonnées
     * sont connues
     *
     * @param integer $immeuble Immeuble concerné par la recherche
     *
     * @return integer
     * @static
     */
    static public function coordonneesDansImmeuble(int $immeuble)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On recherche le nombre de contacts recueillis dans l'immeuble
        $query = 'SELECT COUNT(*)
                  FROM `contacts`
                  WHERE (
                      (`contact_email` > 0 AND `contact_optout_email` = 0)
                      OR (`contact_fixe` > 0 AND `contact_optout_fixe` = 0)
                      OR (`contact_mobile` > 0 AND `contact_optout_mobile` = 0)
                  )
                  AND `contact_optout_global` = 0
                  AND `immeuble_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $immeuble, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne le résultat
        return $data[0];
    }

    /**
     * Retourne s'il existe dans un bureau de vote des fiches où des
     * coordonnées sont connues
     *
     * @param integer $bureau Bureau de vote concerné par la recherche
     *
     * @return integer
     * @static
     */
    static public function coordonneesDansBureau(int $bureau)
    {
        // On lance la connexion à la base de données
        $link = Configuration::read('db.link');

        // On recherche le nombre de contacts recueillis dans l'immeuble
        $query = 'SELECT COUNT(*)
                  FROM `contacts`
                  WHERE (
                      (
                          `contact_email` IS NOT NULL
                          AND `contact_optout_email` = 0
                      )
                      OR (
                          `contact_telephone` IS NOT NULL
                          AND `contact_optout_telephone` = 0
                      )
                      OR (
                          `contact_mobile` IS NOT NULL
                          AND `contact_optout_mobile` = 0
                      )
                  )
                  AND `contact_optout_global` = 0
                  AND `bureau_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':id', $bureau, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);

        // On retourne le résultat
        return $data[0];
    }
}
