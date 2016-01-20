<?php
/**
 * Gestion des contacts
 *
 * PHP version 5
 *
 * @category Contact
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Gestion des contacts
 *
 * PHP version 5
 *
 * @category Contact
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Contact
{
    /**
     * Tableau contenant les informations du contact ouvert
     *
     * @var array
     */
    public $contact = [];

    /**
     * BDD link
     *
     * @var object
     */
    private $_link;

    /**
     * Constructor
     *
     * @param string  $contact  Contact ID (SHA2 hash)
     * @param boolean $securite if fail, return to menu?
     *
     * @return void
     */
    public function __construct(string $contact, $securite = false)
    {
        // On commence par paramétrer les données PDO
        $this->_link = Configuration::read('db.link');

        // On cherche maintenant à savoir s'il existe un contact ayant
        // pour identifiant celui demandé
        $query = 'SELECT *
                  FROM `contacts`
                  WHERE MD5(`contact_id`) = :contact';
        $query = $this->_link->prepare($query);
        $query->bindParam(':contact', $contact);
        $query->execute();
        $contact = $query->fetch(PDO::FETCH_ASSOC);

        // On transforme l'ID de la fiche en md5 pour faciliter
        // sa réutilisation future
        $contact['contact_md5'] = md5($contact['contact_id']);

        // On récupère la liste des tags
        if (!empty($contact['contact_tags'])) {
            $contact['tags'] = explode(',', $contact['contact_tags']);
        } else {
            $contact['tags'] = array();
        }

        // On recherche les coordonnées pour la fiche
        $query = 'SELECT `coordonnee_type`,
                         `coordonnee_numero`
                         `coordonnee_email`
                  FROM `coordonnees`
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare($query);
        $query->bindParam(':id', $contact['contact_id']);
        $query->execute();
        $coordonnees = $query->fetchAll(PDO::FETCH_ASSOC);

        // On prépare les coordonnées
        $contact['email'] = '';
        $contact['mobile'] = '';
        $contact['fixe'] = '';

        // Pour chaque coordonnées, on retraite dans le bon tableau
        foreach ($coordonnees as $coordonnee) {
            if ($coordonnee['coordonnee_type'] == 'email') {
                $contact['email'] = $coordonnee['coordonnee_email'];
            } elseif ($coordonnee['coordonnee_type'] == 'mobile') {
                $contact['mobile'] = Core::getFormatPhone(
                    $coordonnee['coordonnee_numero']
                );
            } else {
                $contact['fixe'] = Core::getFormatPhone(
                    $coordonnee['coordonnee_numero']
                );
            }
        }

        // On entre les données en paramètre de la classe
        $this->contact = $contact;

        // Une fois que c'est le cas, on rajoute le calcul de l'âge
        // pour les exports JSON
        $age = $this->age();
        $this->contact['age'] = $age;

        // Une fois que c'est le cas, on rajoute la détermination
        // de la ville du contact
        $ville = $this->ville();
        $this->contact['ville'] = $ville;

        // On prépare ensuite le nom d'affichage
        $nom_affichage = $this->noms(' ');
        $this->contact['nom_affichage'] = $nom_affichage;
    }

    /**
     * Export all data in JSON
     *
     * @return string
     */
    public function json()
    {
        return json_encode($this->contact);
    }

    /**
     * Return all data
     *
     * @return array
     */
    public function donnees()
    {
        return $this->contact;
    }

    /**
     * Get an asked data
     *
     * @param string $info asked data
     *
     * @return mixed
     */
    public function get(string $info)
    {
        return $this->contact[ $info ];
    }

    /**
     * Return contact's firstname & lastname
     *
     * @param string $separateur Return elements separator
     * @param string $conteneur  HTML Container
     *
     * @return string
     */
    public function noms($separateur = ' ', $conteneur = 'span')
    {
        // On prépare le tableau d'affichage des résultats
        $retour = array();

        // On ajoute le conteneur comprenant le nom, le nom d'usage et les prénoms
        if (!empty($this->contact['contact_nom'])) {
            $retour[] = $this->_contenir(
                mb_convert_case(
                    $this->contact['contact_nom'],
                    MB_CASE_UPPER
                ),
                'span'
            );
        }

        if (!empty($this->contact['contact_nom_usage'])) {
            $retour[] = $this->_contenir(
                mb_convert_case(
                    $this->contact['contact_nom_usage'],
                    MB_CASE_UPPER
                ),
                'span'
            );
        }

        if (!empty($this->contact['contact_prenoms'])) {
            $retour[] = $this->_contenir(
                mb_convert_case(
                    $this->contact['contact_prenoms'],
                    MB_CASE_TITLE
                ),
                'span'
            );
        }

        // On traite le tableau en intégrant le séparateur
        $retour = implode($separateur, $retour);

        // On retourne les informations demandées
        return $retour;
    }

    /**
     * Return birthdate
     *
     * @param string  $separateur      Date separator
     * @param boolean $date_uniquement Date only or text
     *
     * @return array
     */
    public function naissance( $separateur = '/' , $date_uniquement = false )
    {
        // timestamp lié à la date de naissance
        $time = strtotime($this->contact['contact_naissance_date']);

        // on prépare l'affichage dans le tableau $retour
        $retour = array();

        // On prépare la date avec le séparateur choisi
        $retour[] = date('d' . $separateur . 'm' . $separateur . 'Y', $time);

        // On ne prépare les informations relatives aux départements
        // et communes de naissance que si c'est demandé
        if ($date_uniquement === false
            && $this->contact['contact_naissance_commune_id'] != 0
        ) {
            // on récupère les informations géographiques
            $query = 'SELECT `commune_nom`, `departement_id`
                      FROM `communes`
                      WHERE `commune_id` = :commune';
            $query = $this->_link->prepare($query);
            $query->bindParam(
                ':commune',
                $this->contact['contact_naissance_commune_id']
            );
            $query->execute();
            $commune = $query->fetch(PDO::FETCH_ASSOC);

            // on récupère le nom du département
            $query = 'SELECT `departement_nom`
                      FROM `departements`
                      WHERE `departement_id` = :departement';
            $query = $this->_link->prepare($query);
            $query->bindParam(':departement', $commune['departement_id']);
            $query->execute();
            $departement = $query->fetch(PDO::FETCH_ASSOC);

            // On prépare l'affichage de la ville et de son département
            $retour[] = 'à ' . $commune['commune_nom'] .
                        ' (' . ucwords($departement['departement_nom']) . ')';
        }

        // On met en forme le tableau
        $retour = implode(' ', $retour);

        // On retourne l'affichage préparé
        return $retour;
    }

    /**
     * Return contact's age
     * @param  boolean $textuel text or integer?
     * @return string|int
     */
    public function age($textuel = true)
    {
        if ($this->contact['contact_naissance_date'] == '0000-00-00') {
            return 'Âge inconnu';
        } else {
            // Récupération du timestamp de la date de naissance
            $date = strtotime($this->contact['contact_naissance_date']);

            // Traitement de la date de naissance en blocs
            $annee = date('Y', $date);
            $mois = date('m', $date);
            $jour = date('j', $date);

            // On commence par calculer simplement l'âge
            $age = 2014 - $annee;

            // On ajuste par rapport au mois
            if ($mois >= date('m')) {
                // On ajuste par rapport au jour
                if ($jour > date('j')) {
                    $age = $age - 1;
                }
            }

            // On regarde le mode d'affichage et on adapte le retour
            if ($textuel === true) {
                // On retourne l'âge accompagné du mot "an(s)"
                $retour = $age . ' an';

                // On regarde si le pluriel du mot doit être mis ou non
                if ($age > 1) {
                    $retour .= 's';
                }

                // On retourne l'âge
                return $retour;
            } else {
                // On retourne l'âge
                return $age;
            }
        }
    }

    /**
     * Return contact's city
     *
     * @return string
     */
    public function ville()
    {
        // On vérifie si une donnée géographique existe
        if ($this->contact['adresse_id'] > 0
            || $this->contact['immeuble_id'] > 0
        ) {
            // On récupère l'identifiant de l'adresse connue la plus intéressante
            if ($this->contact['adresse_id'] > 0) {
                $adresse = $this->contact['adresse_id'];
            } else {
                $adresse = $this->contact['immeuble_id'];
            }

            // On prépare l'ensemble des requêtes nécessaires à la recherche
            $query = 'SELECT `rue_id`
                      FROM `immeubles`
                      WHERE `immeuble_id` = :immeuble';
            $immeuble = $this->_link->prepare($query);

            $query = 'SELECT `commune_id`
                      FROM `rues`
                      WHERE `rue_id` = :rue';
            $rue = $this->_link->prepare($query);

            $query = 'SELECT `commune_nom`
                      FROM `communes`
                      WHERE `commune_id` = :ville';
            $ville = $this->_link->prepare($query);

            // On mets à jour progressivement les paramètres
            $immeuble->bindParam(':immeuble', $adresse, PDO::PARAM_INT);
            $immeuble->execute();
            $immeuble = $immeuble->fetch(PDO::FETCH_ASSOC);

            $rue->bindParam(':rue', $immeuble['rue_id'], PDO::PARAM_INT);
            $rue->execute();
            $rue = $rue->fetch(PDO::FETCH_ASSOC);

            $ville->bindParam(':ville', $rue['commune_id'], PDO::PARAM_INT);
            $ville->execute();
            $ville = $ville->fetch(PDO::FETCH_ASSOC);
            $ville = $ville['commune_nom'];

            // On retourne le nom de la ville à afficher
            return $ville;
        } else {
            // On affiche l'absence d'adresse
            return 'Aucune adresse connue';
        }
    }

    /**
     * Return contact address, postal format
     *
     * @param string $adresse    which kind of address?
     * @param string $separateur address' elements separator
     *
     * @return string
     */
    public function adresse($adresse = 'electoral', $separateur = '<br>')
    {
        // On récupère l'identifiant de l'adresse demandée
        $type = array('electorale' => 'immeuble', 'declaree' => 'adresse');
        $immeuble = $this->contact[ $type[ $adresse ] . '_id' ];

        // On récupère les informations liées à l'adresse
        $query = 'SELECT `immeuble_numero`, `rue_id`, `immeuble_cp`
                  FROM `immeubles`
                  WHERE `immeuble_id` = :immeuble';
        $query = $this->_link->prepare($query);
        $query->bindParam(':immeuble', $immeuble);
        $query->execute();
        $immeuble = $query->fetch(PDO::FETCH_ASSOC);
        unset($query);

        // On récupère les informations liées à la rue
        $query = 'SELECT `rue_nom`, `commune_id`
                  FROM `rues`
                  WHERE `rue_id` = :rue';
        $query = $this->_link->prepare($query);
        $query->bindParam(':rue', $immeuble['rue_id']);
        $query->execute();
        $rue = $query->fetch(PDO::FETCH_ASSOC);
        unset($query);

        // On récupère les informations liées à la ville
        $query = 'SELECT `commune_nom`
                  FROM `communes`
                  WHERE `commune_id` = :commune';
        $query = $this->_link->prepare($query);
        $query->bindParam(':commune', $rue['commune_id']);
        $query->execute();
        $ville = $query->fetch(PDO::FETCH_ASSOC);
        unset($query);

        // On prépare la mise en forme retournée dans la tableau $retour
        $retour = $immeuble['immeuble_numero'] .
                  ' ' . $rue['rue_nom'] .
                  $separateur .
                  $immeuble['immeuble_cp'] .
                  ' ' . $ville['commune_nom'];

        // On retourne le texte
        return $retour;
    }

    /**
     * Return ballot office
     *
     * @param bool $adresse if true, ballot office number + address
     *
     * @return string
     */
    public function bureau($adresse = false)
    {
        // On récupère les informations sur le bureau de vote
        $query = 'SELECT `bureau_id`,
                         `bureau_code`,
                         `commune_id`,
                         `bureau_nom`,
                         `bureau_adresse`,
                         `bureau_cp`
                  FROM `bureaux`
                  WHERE `bureau_id` = :bureau';
        $query = $this->_link->prepare($query);
        $query->bindParam(':bureau', $this->contact['bureau_id']);
        $query->execute();
        $bureau = $query->fetch(PDO::FETCH_ASSOC);
        unset($query);

        // On effectue la recherche des informations liées
        // à la commune du bureau de vote
        $query = 'SELECT `commune_nom`
                  FROM `communes`
                  WHERE `commune_id` = :commune';
        $query = $this->_link->prepare($query);
        $query->bindParam(':commune', $bureau['commune_id']);
        $query->execute();
        $ville = $query->fetch(PDO::FETCH_ASSOC);
        unset($query);

        // On prépare le retour au sein d'un tableau $retour
        $retour = array();

        // On prépare le nom du bureau de vote et de son numéro
        $ligne = 'Bureau <a href="index.php?page=carto&niveau=bureau&code=' .
                  hash('sha256', $bureau['bureau_id']) . '">' .
                  $bureau['bureau_code'] . '</a> &ndash; ' .
                  $ville['commune_nom'];
        if (!empty($bureau['bureau_nom'])) {
            $ligne .= '<br>' . $bureau['bureau_nom'];
        }

        // On affecte cette ligne au tableau de retour
        $retour[] = $ligne;
        unset($ligne);

        // On prépare l'affichage de l'adresse
        if ($adresse === true) {
            $ligne = $bureau['bureau_adresse'] . '<br>';
            $ligne .= $bureau['bureau_cp'] . ' ' . $ville['commune_nom'];

            // On affecte cette ligne au tableau de retour
            $retour[] = $ligne;
            unset($ligne);
        }

        // On prépare le rendu via le tableau $retour
        $retour = implode('<br>', $retour);

        // On retourne le rendu
        return $retour;
    }

    /**
     * Return registered contacts for a contact
     *
     * @param string $type contact type (email, fixe, mobile)
     *
     * @return array
     */
    public function coordonnees($type = 'toutes')
    {
        // On prépare les requêtes SQL
        if ($type == 'email') {
            $query = 'SELECT `coordonnee_id`,
                             `coordonnee_type`,
                             `coordonnee_email`
                      FROM `coordonnees`
                      WHERE `coordonnee_type` = "email"
                      AND `coordonnee_email` IS NOT NULL
                      AND `contact_id` = :contact
                      ORDER BY `coordonnee_email` ASC';
        } else if ($type == 'fixe') {
            $query = 'SELECT `coordonnee_id`,
                             `coordonnee_type`,
                             `coordonnee_numero`
                      FROM `coordonnees`
                      WHERE `coordonnee_type` = "fixe"
                      AND `coordonnee_numero` IS NOT NULL
                      AND `contact_id` = :contact
                      ORDER BY `coordonnee_numero` ASC';
        } else if ($type == 'mobile') {
            $query = 'SELECT `coordonnee_id`,
                             `coordonnee_type`,
                             `coordonnee_numero`
                      FROM `coordonnees`
                      WHERE `coordonnee_type` = "mobile"
                      AND `coordonnee_numero` IS NOT NULL
                      AND `contact_id` = :contact
                      ORDER BY `coordonnee_numero` ASC';
        } else {
            $query = 'SELECT `coordonnee_id`,
                             `coordonnee_type`,
                             `coordonnee_email`,
                             `coordonnee_numero`
                      FROM `coordonnees`
                      WHERE (
                          `coordonnee_numero` IS NOT NULL
                          OR `coordonnee_email` IS NOT NULL
                      )
                      AND `contact_id` = :contact
                      ORDER BY `coordonnee_type`,
                               `coordonnee_email`,
                               `coordonnee_numero` ASC';
        }

        // On affecte au sein de la requête les données d'identification
        // du contact et on exécute la requête
        $query = $this->_link->prepare($query);
        $query->bindParam(':contact', $this->contact['contact_id']);
        $query->execute();
        $coordonnees = $query->fetchAll();
        unset($query);
        // On retourne le tableau $coordonnees
        return $coordonnees;
    }

    /**
     * Verify if contact data exist for a contact
     *
     * @param string $type contact data type
     *
     * @return boolean
     */
    public function possede(string $type)
    {
        // On retourne un booléen selon le nombre de données trouvées
        if ($this->get('contact_' . $type)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add a DOM container around a text
     *
     * @param string $contenu   content
     * @param string $conteneur container
     *
     * @return string
     */
    private function _contenir($contenu, $conteneur = '')
    {
        // On prépare la variable contenant le retour demandé
        $retour = '';

        // On ajoute l'ouverture du conteneur
        if (!empty($conteneur)) {
            $retour .= '<' . $conteneur . '>';
        }

        // On ajoute le contenu
        $retour .= $contenu;

        // On ajoute la fermeture du conteneur
        if (!empty($conteneur)) {
            $retour .= '</' . $conteneur . '>';
        }

        // On retourne le contenu
        return $retour;
    }

    /**
     * Add a contact data for the contact
     *
     * @param string     $type        data type
     * @param string|int $coordonnees data value
     *
     * @return void
     */
    public function ajoutCoordonnees(string $type, $coordonnees)
    {
        // On prépare la requête selon le type fourni
        if ($type == 'email') {
            $query = 'INSERT INTO `coordonnees` (`contact_id`,
                                                 `coordonnee_type`,
                                                 `coordonnee_email`)
                      VALUES (:contact,
                              :type,
                              :coordonnees)';
        } else {
            $query = 'INSERT INTO `coordonnees` (`contact_id`,
                                                 `coordonnee_type`,
                                                 `coordonnee_numero`)
                      VALUES (:contact,
                              :type,
                              :coordonnees)';
        }

        // On affecte les variables à la requête
        $query = $this->_link->prepare($query);
        $query->bindParam(':contact', $this->contact['contact_id']);
        $query->bindParam(':type', $type);
        $query->bindParam(':coordonnees', $coordonnees);

        // On exécute la requête
        $query->execute();

        // On incrémente le nombre de coordonnées dans la fiche
        $query = 'UPDATE `contacts`
                  SET `contact_' . $type . '` = `contact_' . $type . '` + 1
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare($query);
        $query->bindParam(':id', $this->contact['contact_id']);
        $query->execute();
    }

    /**
     * Return an array with all linked contacts
     *
     * @return array
     */
    public function fichesLiees()
    {
        // On prépare la requête de récupération des fiches liées
        $query = 'SELECT `ficheA`, `ficheB`
                  FROM `liaisons`
                  WHERE `ficheA` = :contact
                  OR `ficheB` = :contact';
        $query = $this->_link->prepare($query);
        $query->bindParam(':contact', $this->contact['contact_id']);

        // On exécute la requête
        $query->execute();
        $fiches = $query->fetchAll();

        // On prépare le tableau des contacts trouvés $contacts
        $contacts = array();

        // Pour chaque résultat, on cherche les noms et prénoms du contact
        foreach ($fiches as $fiche) {
            // On regarde quelle est la deuxième fiche liée,
            // et on l'affecte à la variable $fiche_id
            if ($fiche['ficheA'] == $this->contact['contact_id']) {
                $fiche_id = $fiche['ficheB'];
            } else {
                $fiche_id = $fiche['ficheA'];
            }

            // On prépare la requête de recherche
            $query = 'SELECT `contact_nom`,
                             `contact_nom_usage`,
                             `contact_prenoms`
                      FROM `contacts`
                      WHERE `contact_id` = :contact';
            $query = $this->_link->prepare($query);
            $query->bindParam(':contact', $fiche_id);

            // On exécute la requête et on ajoute les informations
            // au tableau des contacts trouvés $contacts
            $query->execute();
            $infos = $query->fetch(PDO::FETCH_ASSOC);
            $contacts[$fiche_id] = $infos;
        }

        // On retourne le tableau final des contacts liés
        return $contacts;
    }

    /**
     * Return an array with all events
     *
     * @return array
     */
    public function listeEvenements()
    {
        // On exécute la recherche PDO et on récupère les informations dans un
        // tableau $evenements
        $query = 'SELECT `historique_id`
                  FROM `historique`
                  WHERE `contact_id` = :contact
                  ORDER BY `historique_date` DESC';
        $query = $this->_link->prepare($query);
        $query->bindParam(':contact', $this->contact['contact_id']);
        $query->execute();
        $evenements = $query->fetchAll();

        // On retourne le tableau trouvé
        return $evenements;
    }

    /**
     * Update an information in the database
     *
     * @param string $info  information to update
     * @param string $value new value
     *
     * @return void
     */
    public function update(string $info, string $value)
    {
        // On prépare la requête de mise à jour des données
        $query = 'UPDATE `contacts`
                  SET `' . $info . '` = :value
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare();

        // On affecte les variables
        $query->bindParam(':id', $this->contact['contact_id']);
        $query->bindParam(':value', $value);

        // On lance la requête
        $query->execute();

        // On enregistre dans les propriétés les informations
        $this->contact[$info] = $value;
    }

    /**
     * Add a tag
     *
     * @param string $tag tag to add
     *
     * @return void
     */
    public function addTag(string $tag)
    {
        // On récupère la liste des tags et on la transforme en array
        $tags = $this->contact['contact_tags'];
        $tags = explode(',', $tags);

        // On prépare la liste des tags entrés sous forme de tableaux
        $tag = explode(',', $tag);

        // On rassemble les deux tableaux
        $tags = array_merge($tags, $tag);

        // On supprime les doublons
        $tags = array_unique($tags);

        // On fabrique le champ texte à insérer dans la base de données
        $tags = implode(',', $tags);

        // On supprime les premières et dernières virgules
        $tags = trim($tags, ',');

        // On prépare la requête d'insertion dans la base de données
        $query = 'UPDATE `contacts`
                  SET `contact_tags` = :tags
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare($query);

        // On insère les paramètres dans la requête
        $query->bindParam(':id', $this->contact['contact_id'], PDO::PARAM_INT);
        $query->bindParam(':tags', $tags);

        // On exécute la requête
        $query->execute();
    }

    /**
     * Delete a tag
     *
     * @param string $tag tag to delete
     *
     * @return void
     */
    public function deleteTag(string $tag)
    {
        // On récupère la liste des tags
        $tags = $this->contact['contact_tags'];

        // On la retraite sous forme de tableau
        $tags = explode(',', $tags);

        // On regarde si on trouve le tag à supprimer dans la base de données
        if (array_search($tag, $tags)) {
            $cle = array_search($tag, $tags);

            // On retire le tag de la liste en question
            unset($tags[$cle]);
        }

        // On reformate sous forme de chaîne de caractère la liste
        $tags = implode(',', $tags);

        // On supprime les virgules de début et de fin
        $tags = trim($tags, ',');

        // On enregistre le tout dans la base de données
        $query = 'UPDATE `contacts`
                  SET `contact_tags` = :tags
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare($query);

        // On insère les paramètres dans la requête
        $query->bindParam(':id', $this->contact['contact_id'], PDO::PARAM_INT);
        $query->bindParam(':tags', $tags);

        // On exécute la requête
        $query->execute();
    }

    /**
     * Update a file
     *
     * @param mixed         $file       file
     * @param array         $data       file's data
     * @param array|boolean $extensions accepted extensions
     * @param integer       $maxsize    maxsize
     *
     * @return void
     */
    public function uploadFile(
        $file,
        array $data,
        $extensions = false,
        $maxsize = false
    ) {
        // On commence par déterminer le nom du fichier
        $extension = substr(strrchr($file['name'], '.'), 1);
        $nom = preg_replace("#[^a-z0-9]#", "-", strtolower($data['titre'])) .
               '-' .
               time() .
               '.' .
               $extension;

        // test1 : on vérifie que le fichier s'est uploadé correctement
        if (!isset($file) || $file['error'] > 0) {
            return false;
        }

        // test2 : on vérifie si on ne dépasse pas la taille limite
        if ($maxsize !== false && $file['size'] > $maxsize) {
            return false;
        }

        // test3 : on vérifie si l'extension est autorisée
        $extension = substr(strrchr($file['name'], '.'), 1);
        if ($extensions !== false && !in_array($extension, $extensions)) {
            return false;
        }

        // On détermine la destination
        $destination = 'uploads/' . $nom;

        // Dans ce cas, on déplace le fichier à sa destination finale
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // On récupère l'ID de l'utilisateur
            if (isset($_COOKIE['leqg-user'])) {
                $utilisateur = $_COOKIE['leqg-user'];
            } else {
                $utilisateur = 0;
            }

            // On enregistre le fichier dans la base de données
            $query = 'INSERT INTO `fichiers` (`contact_id`,
                                              `compte_id`,
                                              `interaction_id`,
                                              `fichier_nom`,
                                              `fichier_description`,
                                              `fichier_url`)
                      VALUES (:contact,
                              :compte,
                              :interaction,
                              :nom,
                              :description,
                              :url)';
            $query = $this->_link->prepare($query);
            $query->bindParam(':contact', $this->contact['contact_id']);
            $query->bindParam(':compte', $utilisateur);
            $query->bindParam(':interaction', $data['evenement']);
            $query->bindParam(':nom', $data['titre']);
            $query->bindParam(':description', $data['description']);
            $query->bindParam(':url', $nom);

            // On exécute l'ajout à la base de données
            $query->execute();

            // On retourne que tout s'est bien passé
            return true;
        } else {
            // On retourne l'erreur
            return false;
        }
    }

    /**
     * Change declared sex
     *
     * @return void
     */
    public function changementSexe()
    {
        // On paramètre le tableau de changement de sexe
        $tableau = array(
            'M' => 'F',
            'F' => 'i',
            'i' => 'M'
        );

        // On prépare la requête
        $query = 'UPDATE `contacts`
                  SET `contact_sexe` = :sexe
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare($query);
        $query->bindParam(':id', $this->contact['contact_id']);
        $query->bindParam(':sexe', $tableau[$this->contact['contact_sexe']]);

        // On exécute la requête
        $query->execute();
    }

    /**
     * Update informations
     *
     * @param string $info   updated information
     * @param string $valeur value
     *
     * @return void
     */
    public function modification(string $info, string $valeur)
    {
        // On prépare la requête
        $query = 'UPDATE `contacts`
                  SET `' . $info . '` = :valeur
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare($query);
        $query->bindParam(':id', $this->contact['contact_id']);
        $query->bindParam(':valeur', $valeur);

        // On exécute la variable
        $query->execute();
    }

    /**
     * Destroy a contact
     *
     * @return void
     */
    public function destruction()
    {
        // On supprime la fiche contact
        $query = 'DELETE FROM `contacts`
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare($query);
        $query->bindParam(':id', $this->contact['contact_id']);
        $query->execute();

        // On supprime tous les événements reliés à cette fiche
        $query = 'DELETE FROM `historique`
                  WHERE `contact_id` = :id';
        $query = $this->_link->prepare($query);
        $query->bindParam(':id', $this->contact['contact_id']);
        $query->execute();
    }

    /**
     * Create a new contact and return the ID
     *
     * @return integer new contact ID
     * @static
     */
    static public function creation()
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');

        // On prépare la requête de création
        $query = 'INSERT INTO `contacts` (`contact_electeur`)
                  VALUES (0)';
        $query = $link->prepare($query);

        // On exécute la requête
        $query->execute();

        // On récupère l'identifiant
        $id = $link->lastInsertId();

        // On retourne cet identifiant
        return $id;
    }

    /**
     * Search a contact and return all possibilities
     *
     * @param string $recherche search terms
     *
     * @return array all matches ID
     * @static
     */
    static public function recherche(string $recherche)
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');

        // On regarde s'il s'agit de la recherche d'une date
        $date = DateTime::createFromFormat('d/m/Y', $recherche);

        if ($date) {
            $recherche = $date->format('Y-m-d');

            // On prépare la requête de récupération des résultats
            $query = 'SELECT `contact_id`
                      FROM `contacts`
                      WHERE `contact_naissance_date` = :date
                      ORDER BY `contact_nom` ASC,
                               `contact_nom_usage` ASC,
                               `contact_prenoms` ASC';
            $query = $link->prepare($query);

            // On affecte la date à la recherche
            $query->bindParam(':date', $recherche);

            // On exécute la recherche et on vérifie qu'il n'y a pas d'erreurs
            if (!$query->execute()) {
                return false;
            }

            // On retraite les données de la requête
            $resultats = $query->fetchAll(PDO::FETCH_ASSOC);

            // On retourne les résultats
            return $resultats;
        } else {
            // On prépare la requête de récupération des résultats
            $query = 'SELECT `contact_id`
                      FROM `contacts`
                      WHERE CONCAT_WS(" ",
                                      contact_prenoms,
                                      contact_nom,
                                      contact_nom_usage,
                                      contact_nom,
                                      contact_prenoms) LIKE :terme
                      ORDER BY `contact_nom` ASC,
                               `contact_nom_usage` ASC,
                               `contact_prenoms` ASC';
            $query = $link->prepare($query);

            // On prépare le terme à affecter à la recherche en remplaçant
            // tous les espaces et caractères non alphabétiques par des jokers
            $terme = trim($recherche);
            $terme = preg_replace('#[^[:alpha:]]#u', '%', $terme);
            $terme = "%$terme%";

            // On affecte le terme à rechercher à la requête
            $query->bindValue(':terme', $terme);

            // On exécute et on vérifie qu'il n'y a pas d'erreurs d'exécution
            if (!$query->execute()) {
                return false;
            }

            // On retraite les données de la requête
            $resultats = $query->fetchAll(PDO::FETCH_ASSOC);

            // On retourne les résultats
            return $resultats;
        }
    }

    /**
     * Search contacts by tag
     *
     * @param string $terme searched tag
     *
     * @return array all matches contact
     * @static
     */
    static public function rechercheThematique( $terme )
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');

        // On prépare le tableau global des contacts résultant de la recherche
        $contacts = [];

        // On prépare le terme aux like
        $terme = "%$terme%";

        // On effectue une première recherche sur les tags des fiches
        $query = 'SELECT `contact_id`
                  FROM `contacts`
                  WHERE `contact_tags` LIKE :terme
                  ORDER BY `contact_nom`,
                           `contact_nom_usage`,
                           `contact_prenoms` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':terme', $terme);
        $query->execute();
        if ($query->rowCount() >= 1) {
            $resultats = $query->fetchAll(PDO::FETCH_NUM);
            foreach ($resultats as $resultat) {
                $contacts[] = $resultat[0];
            }
            unset($resultats);
        }

        // On continue en vérifiant les objets d'événements
        $query = 'SELECT `contact_id`
                  FROM `historique`
                  WHERE `historique_objet` LIKE :terme
                  OR `historique_notes` LIKE :terme
                  ORDER BY `contact_id` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':terme', $terme);
        $query->execute();
        if ($query->rowCount() >= 1) {
            $resultats = $query->fetchAll(PDO::FETCH_NUM);
            foreach ($resultats as $resultat) {
                $contacts[] = $resultat[0];
            }
            unset($resultats);
        }

        // On continue en vérifiant les fichiers
        $query = 'SELECT `contact_id`
                  FROM `fichiers`
                  WHERE `fichier_nom` LIKE :terme
                  OR `fichier_description` LIKE :terme
                  ORDER BY `contact_id` ASC';
        $query = $link->prepare($query);
        $query->bindParam(':terme', $terme);
        $query->execute();
        if ($query->rowCount() >= 1) {
            $resultats = $query->fetchAll(PDO::FETCH_NUM);
            foreach ($resultats as $resultat) {
                $contacts[] = $resultat[0];
            }
            unset($resultats);
        }

        // À la fin, on vérifie qu'il n'existe pas de doublons
        $contacts = array_unique($contacts);

        // On retourne la liste des contacts concernés par cette recherche
        return $contacts;
    }

    /**
     * List all contacts by criteria
     *
     * @param array       $tri    sorting
     * @param int|boolean $debut  order, true if you only want a count
     * @param integer     $nombre number of contacts
     *
     * @return array
     * @static
     */
    static function listing(array $tri, $debut, $nombre = 5)
    {
        if ((is_numeric($debut) || is_bool($debut))
            && (is_numeric($nombre) || is_bool($nombre))
            && is_array($tri)
        ) {
            // On prépare le lien vers la BDD
            $link = Configuration::read('db.link');

            if (is_bool($debut) && $debut === true) {
                // On commence par préparer le visage de la requête de recherche
                $sql = 'SELECT COUNT(`contact_id`) AS `nombre`
                        FROM `contacts` ';
            } else {
                // On commence par préparer le visage de la requête de recherche
                $sql = 'SELECT `contact_id`
                        FROM `contacts` ';
            }

            // On va chercher à y ajouter les différents critères,
            // à travers un tableau $criteres
            $criteres = array();

            // On retraite le critère "email"
            if ($tri['email']) {
                // Si le tri demandé concerne les fiches avec l'information
                if ($tri['email'] == 2) {
                    $criteres[] = '`contact_email` > 0';
                } else {
                    $criteres[] = '`contact_email` = 0';
                }
            }

            if ($tri['adresse']) {
                // Si le tri demandé: les fiches avec forcément une adresse
                if ($tri['adresse'] == 2) {
                    $criteres[] = '(`adresse_id` != 0 OR `immeuble_id` != 0)';
                } else {
                    $criteres[] = '(`adresse_id` = 0 AND `immeuble_id` = 0)';
                }
            }

            // On retraite le critère "mobile"
            if ($tri['mobile']) {
                // Si le tri demandé concerne les fiches avec l'information
                if ($tri['mobile'] == 2) {
                    $criteres[] = '`contact_mobile` > 0';
                } else {
                    $criteres[] = '`contact_mobile` = 0';
                }
            }

            // On retraite le critère "fixe"
            if ($tri['fixe']) {
                // Si le tri demandé concerne les fiches avec l'information
                if ($tri['fixe'] == 2) {
                    $criteres[] = '`contact_fixe` > 0';
                } else {
                    $criteres[] = '`contact_fixe` = 0';
                }
            }

            // On retraite le critère "phone"
            if (isset($tri['phone']) && $tri['phone']) {
                // Si le tri demandé concerne les fiches avec l'information
                if ($tri['phone'] == 2) {
                    $criteres[] = '(`contact_fixe` > 0 OR `contact_mobile` > 0)';
                }
            }

            // On retraite le critère "electeur"
            if ($tri['electeur']) {
                $criteres[] = '`contact_electeur` = ' .
                               ($tri['electeur'] - 1);
            }

            // Si des critères plus complexes sont demandés, on s'en occupe ici
            if (!empty($tri['criteres'])) {
                $criteria = explode(';', $tri['criteres']);

                // On prépare les tableaux avec les différents critères
                $themas = [];
                $birth = [];
                $bureaux = [];
                $rues = [];
                $votes = [];

                // On sépare les critères de leurs type
                foreach ($criteria as $key => $val) {
                    $crit = explode(':', $val);

                    if ($crit[0] == 'thema') {
                        $themas[] = $crit[1];
                    } else if ($crit[0] == 'birth') {
                        $birth[] = $crit[1];
                    } else if ($crit[0] == 'bureau') {
                        $bureaux[] = $crit[1];
                    } else if ($crit[0] == 'rue') {
                        $rues[] = $crit[1];
                    } else if ($crit[0] == 'vote') {
                        $votes[] = $crit[1];
                    }
                }

                // On va analyser les critères thématiques
                if (count($themas)) {
                    // On va ajouter chaque condition thématique à la recherche
                    foreach ($themas as $thema) {
                        $thema = preg_replace('#[^[:alnum:]]#u', '%', $thema);
                        $criteres[] = '`contact_tags` LIKE "%' . $thema . '%"';
                    }
                }

                // On va analyser les critères de votes
                if (count($votes)) {
                    foreach ($votes as $vote) {
                        $criteres[] = '`contact_vote_' . $vote . '` = 1';
                    }
                }

                // On va analyser les critères de naissance
                if (count($birth)) {
                    // On va ajouter chaque condition de naissance à la recherche
                    // $dates en retraitant son format
                    $dates = array();

                    foreach ($birth as $date) {
                        $date = explode('/', $date);
                        krsort($date);
                        $date = implode('-', $date);
                        $dates[] = '`contact_naissance_date` = "' . $date . '"';
                    }

                    if (count($dates) == 1) {
                        $criteres[] = $dates[0];
                    } else {
                        $criteres[] = '(`contact_naissance_date` = "' .
                                       implode('" OR `contact_naissance_date`="') .
                                       '")';
                    }
                }

                // On va analyser les bureaux de votes demandés
                if (count($bureaux)) {
                    $ids = implode(',', $bureaux);

                    // On prépare la condition SQL
                    $criteres[] = '`bureau_id` IN (' . $ids . ')';
                }


                // On va analyser toutes les rues demandées pour récupérer
                // tous les ID d'immeubles concernées par ces rues
                // et les électeurs qui y sont
                if (count($rues)) {
                    // Pour chaque rue, on cherche les immeubles concernés
                    $immeubles = array();
                    foreach ($rues as $rue) {
                        $query = 'SELECT `immeuble_id`
                                  FROM `immeubles`
                                  WHERE `rue_id` = :id';
                        $query = $link->prepare($query);
                        $query->bindParam(':id', $rue);
                        $query->execute();
                        $ids = $query->fetchAll(PDO::FETCH_NUM);

                        // Pour chaque immeuble trouvé, on le rajoute
                        foreach ($ids as $id) {
                            $immeubles[] = $id[0];
                        }
                    }

                    // On transforme cette liste d'immeuble
                    $ids = implode(',', $immeubles);

                    // On rajoute la requête aux conditions SQL
                    $criteres[] = '( `immeuble_id` IN (' . $ids . ')
                                     OR `adresse_id` IN (' . $ids . ') )';
                }
            }


            // On retraite les critères en conditions SQL
            if ($criteres) {
                $sql .= ' WHERE ' . implode(' AND ', $criteres);
            }

            // On ajoute les conditions de nombre et d'ordre
            if ($nombre && !is_bool($debut)) {
                $sql .= ' ORDER BY `contact_nom`,
                                   `contact_nom_usage`,
                                   `contact_prenoms` ASC
                          LIMIT ' . $debut . ', ' . $nombre;
            } else {
                $sql .= ' ORDER BY `contact_nom`,
                                   `contact_nom_usage`,
                                   `contact_prenoms` ASC';
            }

            // On exécute la requête SQL
            $query = $link->prepare($sql);
            $query->execute();

            // Si on souhaite uniquement une estimation, on retourne le nombre
            if (is_bool($debut) && $debut === true) {
                $nombre = $query->fetch(PDO::FETCH_NUM);
                return $nombre[0];
            } else {
                // On retraite la liste des identifiants pour en faire
                // un tableau PHP $contacts
                $ids = $query->fetchAll(PDO::FETCH_NUM);
                $contacts = array();
                foreach ($ids as $id) {
                    $contacts[] = $id[0];
                }

                // On retourne la liste des ids de fiches
                return $contacts;
            }
        } else {
            // On retourne une erreur
            return false;
        }
    }

    /**
     * List all last created contact
     *
     * @param integer $nombre number of contacts
     *
     * @return array
     * @static
     */
    static public function last($nombre = 5)
    {
        // On commence par paramétrer les données PDO
        $link = Configuration::read('db.link');

        // On prépare le tableau de résultat de la requête
        $fiches = array();

        // On prépare la requête
        $sql = 'SELECT `contact_id`
                FROM `contacts`
                ORDER BY `contact_id` DESC
                LIMIT 0, ' . $nombre;
        $query = $link->prepare($sql);

        // On exécute la requête
        if ($query->execute()) {
            // On affecte les résultats au tableau des fiches
            $ids = $query->fetchAll(PDO::FETCH_ASSOC);
            // On retraite les informations pour les ajouter au tableau $fiches
            foreach ($ids as $id) {
                $fiches[] = $id['contact_id'];
            }
            // On retourne le tableau correspondant
            return $fiches;
        } else {
            // On retourne une erreur
            return false;
        }
    }
}
