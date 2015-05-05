<?php
/**
 * LeQG – political database manager
 * People class
 *
 * PHP Version 5.5.21
 *
 * @category  LeQG
 * @package   LeQG
 * @author    Damien Senger <hi@hiwelo.co>
 * @copyright 2014-2015 MSG SAS – LeQG
 * @license   Personal Use Only
 * @version   GIT:<git@github.com:hiwelo/leqg.git>
 * @link      http://hiwelo.co/
 * */

/**
 * LeQG – political database manager
 * People class
 *
 * PHP Version 5.5.21
 *
 * @category  LeQG
 * @package   LeQG
 * @author    Damien Senger <hi@hiwelo.co>
 * @copyright 2014-2015 MSG SAS – LeQG
 * @license   Personal Use Only
 * @link      http://hiwelo.co/
 * */
class People
{

    /**
     * @var array   $_people        People data
     * @var array   $_address       Address data
     * @var array   $_postal        Complete postal addresses
     * */
    private $_people, $_address, $_postal;


    /**
     * Construct method
     *
     * @param   string  $person     asked person ID (w/o hash)
     * @result  void
     * */
    public function __construct($person)
    {
        $query = Core::query('person-load');
        $query->bindValue(':person', $person, PDO::PARAM_INT);
        $query->execute();
        $this->_people = $query->fetch(PDO::FETCH_ASSOC);

        // tag parsing
        if (!empty($this->_people['tags'])) {
            $this->_people['tags'] = explode(',', $this->_people['tags']);
        } else {
            $this->_people['tags'] = array();
        }

        // address & postal data
        $this->_address = self::address($this->_people['id']);
        $this->_postal = self::postal($this->_address);

        // age calculator
        $this->_people['age'] = $this->display_age();

        // display name
        $this->_people['nom_complet'] = $this->display_name();
    }


    /**
     * Get an asked information
     *
     * @param   string  $data   Asked information
     * @result  mixed
     * */
    public function get($data)
    {
        return $this->_people[$data];
    }


    /**
     * Update an asked information
     *
     * @param   string  $data   Data to update
     * @param   string  $value  New value
     * @result  void
     * */
    public function update($data, $value)
    {
        if ($data != 'tags') $this->_people[$data] = $value;
        $link = Configuration::read('db.link');
        $query = $link->prepare('UPDATE `people` SET `'.$data.'` = :value WHERE `id` = :id');
        $query->bindValue(':id', $this->_people['id']);
        $query->bindValue(':value', $value);
        $query->execute();
    }


    /**
     * Personal data JSON export
     *
     * @result  string
     * */
    public function json()
    {
        $data = $this->_people;
        $data['nom_complet'] = $this->display_name();

        return json_encode($data);
    }


    /**
     * Personal data export
     *
     * @result  array
     * */
    public function data()
    {
        $data = $this->_people;
        $data['nom_complet'] = $this->display_name();

        return $data;
    }


    /**
     * Display name generator method
     *
     * @result  string
     * */
    public function display_name()
    {
        $display = array();
        if (!empty($this->_people['nom'])) $display[] = mb_convert_case($this->_people['nom'], MB_CASE_UPPER);
        if (!empty($this->_people['nom_usage'])) $display[] = mb_convert_case($this->_people['nom_usage'], MB_CASE_UPPER);
        if (!empty($this->_people['prenoms'])) $display[] = mb_convert_case($this->_people['prenoms'], MB_CASE_TITLE);

        return implode(' ', $display);
    }


    /**
     * Return birthdate
     *
     * @result  string
     * */
    public function birthdate()
    {
        if (!is_null($this->_people['date_naissance'])) {
            $birthdate = new DateTime($this->_people['date_naissance']);
            return $birthdate->format('d/m/Y');
        } else {
            return false;
        }
    }


    /**
     * Age method
     *
     * @result  int
     * */
    public function age()
    {
        if (!is_null($this->_people['date_naissance'])) {
            return DateTime::createFromFormat('Y-m-d', $this->_people['date_naissance'])
                 ->diff(new Datetime('now'))
                 ->y;
        } else {
            return false;
        }
    }


    /**
     * Age displaying method
     *
     * @result  string
     * */
    public function display_age()
    {
        if ($this->age() && $this->age() > 100) {
            return 'Âge inconnu';
        } elseif ($this->age()) {
            return $this->age().' ans';
        } else {
            return 'Âge inconnu';
        }
    }


    /**
     * City method
     *
     * @result  string
     * */
    public function city_copie()
    {
        if ($this->_address['reel']['city'] || $this->_address['officiel']['city']) {
            $query = Core::query('city-by-id');

            if ($this->_address['reel']['city']) {
                $query->bindValue(':city', $this->_address['reel']['city'], PDO::PARAM_INT);
            } else {
                $query->bindValue(':city', $this->_address['officiel']['city'], PDO::PARAM_INT);
            }
            $query->execute();
            return $query->fetch(PDO::FETCH_NUM)[0];
        } else {
            return 'Ville inconnue';
        }
    }


    /**
     * Return person city
     *
     * @result  string
     * */
    public function city()
    {
        if (!empty($this->_postal['officiel']['city'])) {
            return $this->_postal['officiel']['city'];
        } elseif (!empty($this->_postal['reel']['city'])) {
            return $this->_postal['reel']['city'];
        } else {
            return 'Aucune ville connue';
        }
    }


    /**
     * Return postal address
     *
     * @result  array
     * */
    public function postal_address()
    {
        $display['officiel'] = '';
        if (!empty($this->_postal['officiel']['building'])) $display['officiel'] .= $this->_postal['officiel']['building'].' ';
        if (!empty($this->_postal['officiel']['street'])) $display['officiel'] .= $this->_postal['officiel']['street'].' ';
        if (!empty($this->_postal['officiel']['zipcode'])) $display['officiel'] .= $this->_postal['officiel']['zipcode'].' ';
        if (!empty($this->_postal['officiel']['city'])) $display['officiel'] .= $this->_postal['officiel']['city'];

        $display['reel'] = '';
        if (!empty($this->_postal['reel']['building'])) $display['reel'] .= $this->_postal['reel']['building'].' ';
        if (!empty($this->_postal['reel']['street'])) $display['reel'] .= $this->_postal['reel']['street'].' ';
        if (!empty($this->_postal['reel']['zipcode'])) $display['reel'] .= $this->_postal['reel']['zipcode'].' ';
        if (!empty($this->_postal['reel']['city'])) $display['reel'] .= $this->_postal['reel']['city'];

        return $display;
    }


    /**
     * Return postal address, separated data
     *
     * @result  array
     * */
    public function postal_array()
    {
        return $this->_postal;
    }


    /**
     * Contact details
     *
     * @param   string  $type       Contact details type, null for all
     * @result  array
     * */
    public function contact_details($type = null)
    {
        switch ($type) {
            case 'email':
                $query = Core::query('contact-details-person-emails');
                break;

            case null:
                $query = Core::query('contact-details-person-all');
                break;

            default:
                $query = Core::query('contact-details-person-phone');
                break;
        }
        $query->bindValue(':contact', $this->_people['id'], PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll();
    }


    /**
     * Check if a specific contact detail exist
     *
     * @param   string  $type       Contact detail type to check
     * @result  bool
     * */
    public function contact_details_exist($type)
    {
        $query = Core::query('contact-details-checking');
        $query->bindValue(':contact', $this->_people['id'], PDO::PARAM_INT);
        $query->bindValue(':type', $type);
        $query->execute();
        $count = $query->fetch(PDO::FETCH_NUM)[0];

        if ($count) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Add a contact detail
     *
     * @param   string  $detail     Contact detail to add
     * @param   string  $type       Contact detail type
     * @result  void
     * */
    public function contact_details_add($detail, $type = null)
    {
        if (filter_var($detail, FILTER_VALIDATE_EMAIL)) {
            $query = Core::query('contact-details-add-email');
            $type = "email";
        } else {
            $query = Core::query('contact-details-add-phone');

            $detail = str_replace(' ', '', $detail);
            $detail = str_replace('+', '00', $detail);
            $detail = str_replace('.', '', $detail);
            $detail = str_replace('/', '', $detail);

            if (is_null($type)) $type = "fixe";
        }

        $query->bindValue(':contact', $this->_people['id'], PDO::PARAM_INT);
        $query->bindValue(':type', $type);
        $query->bindValue(':detail', $detail);
        $query->execute();
    }


    /**
     * Remove a contact detail
     *
     * @param   int     $detail     Contact detail to remove
     * @result  void
     * */
    public function contact_details_remove($detail)
    {
        $query = Core::query('contact-details-remove');
        $query->bindValue(':id', $detail, PDO::PARAM_INT);
        $query->execute();
    }


    /**
     * File upload for an asked person
     *
     * @param   mixed   $file           Uploaded file
     * @param   array   $data           Linked data
     * @param   array   $extensions     Auth extensions
     * @param   int     $maxsize        File max allowed size
     * @result  bool
     * */
    public function file_upload($file, array $data, $extensions = false, $maxsize = false)
    {
        $extension = substr(strrchr($file['name'], '.'), 1);
        $nom = preg_replace("#[^a-zA-Z0-9]#", "-", strtolower($data['titre'])) . '-' . uniqid() . '.' . $extension;

        if (!isset($file) || $file['error'] > 0) return false;
        if ($maxsize !== FALSE && $file['size'] > $maxsize) return false;
        if ($extensions !== FALSE && !in_array($extension, $extensions)) return false;

        $destination = 'uploads/'.$nom;

        if (move_uploaded_file($file['tmp_name'], $destination))
        {
            $utilisateur = User::ID();

            $query = Core::query('file-upload');
            $query->bindValue(':people', $this->_people['id'], PDO::PARAM_INT);
            $query->bindValue(':user', $utilisateur, PDO::PARAM_INT);
            $query->bindValue(':event', $data['evenement'], PDO::PARAM_INT);
            $query->bindValue(':name', $data['titre']);
            $query->bindValue(':desc', $data['description']);
            $query->bindValue(':url', $nom);
            $query->execute();

            return true;
        } else {
            return false;
        }
    }


    /**
     * Linked people list
     *
     * @result  array
     * */
    public function linked_people()
    {
        $query = Core::query('person-linked-people');
        $query->bindValue(':person', $this->_people['id'], PDO::PARAM_INT);
        $query->execute();
        $people = $query->fetchAll(PDO::FETCH_ASSOC);
        $linked_people = array();

        foreach ($people as $person) {
            if ($person['ficheA'] == $this->_people['id']) {
                $linked_people[] = $person['ficheB'];
            } else {
                $linked_people[] = $person['ficheA'];
            }
        }

        return $linked_people;
    }


    /**
     * Events list
     *
     * @result  array
     * */
    public function events()
    {
        $query = Core::query('person-events');
        $query->bindValue(':person', $this->_people['id'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Add a new tag
     *
     * @param   string  $tag        Tag to add
     * @result  void
     * */
    public function tag_add($tag)
    {
        $tags = $this->_people['tags'];

        $tagsToAdd = explode(',', $tag);
        $tags = array_merge($tags, $tagsToAdd);
        $tags = array_unique($tags);

        $this->_people['tags'] = $tags;
        $tags = implode(',', $tags);
        $tags = trim($tags, ',');
        $this->update('tags', $tags);
    }


    /**
     * Remove an asked tag
     *
     * @param   string  $tag        Tag to remove
     * @result  void
     * */
    public function tag_remove($tag)
    {
        $tags = $this->_people['tags'];

        if (in_array($tag, $tags)) {
            $cle = array_search($tag, $tags);
            unset($tags[$cle]);
        }

        $this->_people['tags'] = $tags;
        $tags = implode(',', $tags);
        $tags = trim($tags, ',');
        $this->update('tags', $tags);
    }


    /**
     * Display sex
     *
     * @param   bool    $generic    if true, display sex therefore unknown
     * @result  string
     * */
    public function display_sex($generic = false)
    {
        switch ($this->_people['sexe']) {
            case 'H':
                return 'Homme';
                break;

            case 'F':
                return 'Femme';
                break;

            default:
                if ($generic) {
                    return 'Sexe';
                } else {
                    return 'Inconnu';
                }
                break;
        }
    }


    /**
     * Change sex data
     *
     * @result  void
     * */
    public function change_sex()
    {
        switch ($this->_people['sexe']) {
            case 'H':
                $this->update('sexe', 'F');
                break;

            case 'F':
                $this->update('sexe', null);
                break;

            case null:
                $this->update('sexe', 'H');
                break;
        }
    }


    /**
     * Delete this person
     *
     * @result  void
     * */
    public function delete()
    {
        $query = Core::query('person-delete');
        $query->bindValue(':person', $this->_people['id'], PDO::PARAM_INT);
        $query->execute();
    }


    /**
     * Create a new person
     *
     * @result  int                 new person id
     * */
    public static function create()
    {
        $query = Core::query('person-new');
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }


    /**
     * Address returning for an asked contact
     *
     * @param   string  $person     asked person ID (w/o hash)
     * @result  array
     * */
    public static function address($person)
    {
        $query = Core::query('person-address');
        $query->bindValue(':person', $person, PDO::PARAM_INT);
        $query->execute();
        $addresses = $query->fetchAll(PDO::FETCH_ASSOC);
        $data = array();

        foreach ($addresses as $address) {
            $type = $address['type'];
            unset($address['type']);
            $data[$type] = $address;
        }

        return $data;
    }


    /**
     * Postal address for an asked contact
     *
     * @param   mixed   $data       if array, address elements ID
     *                              or if numeric, asked person ID (w/o hash)
     * @result  array
     * */
    public static function postal($data)
    {
        $postal = array();

        if (is_numeric($data)) {
            $addresses = self::address($data);
        } else {
            $addresses = $data;
        }

        foreach ($addresses as $type => $address) {
            $query = Core::query('city-data');
            $query->bindValue(':city', $address['city']);
            $query->execute();
            $city = $query->fetch(PDO::FETCH_NUM);
            $postal[$type]['country'] = $city[3];
            $postal[$type]['city'] = $city[0];

            $query = Core::query('zipcode-data');
            $query->bindValue(':zipcode', $address['zipcode']);
            $query->execute();
            $zipcode = $query->fetch(PDO::FETCH_NUM);
            $postal[$type]['zipcode'] = $zipcode[0];

            $query = Core::query('street-data');
            $query->bindValue(':street', $address['street']);
            $query->execute();
            $street = $query->fetch(PDO::FETCH_NUM);
            $postal[$type]['street'] = $street[0];

            $query = Core::query('building-data');
            $query->bindValue(':building', $address['building']);
            $query->execute();
            $building = $query->fetch(PDO::FETCH_NUM);
            $postal[$type]['building'] = $building[0];
        }

        return $postal;
    }


    /**
     * Poll informations for an asked person
     *
     * @param   int     $person     asked person id, or poll id if $poll is true
     * @param   bool    $poll       true if $person is a poll id
     * @result  array
     * */
    public static function poll($person, $poll = false)
    {
        if (!$poll) {
            $query = Core::query('person-load');
            $query->bindValue(':person', $person, PDO::PARAM_INT);
            $query->execute();
            $person = $query->fetch(PDO::FETCH_ASSOC);
        } else {
            $person = $this->_people;
        }

        $query = Core::query('poll-data');
        $query->bindValue(':poll', $person['bureau'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * People searching method
     *
     * @param   string  $search     Search term
     * @result  array
     * */
    public static function search($search)
    {
        $birthdate = DateTime::createFromFormat('d/m/Y', $search);

        if ($birthdate) {
            $search = $birthdate->format('Y-m-d');
            $query = Core::query('person-search-birthdate');
            $query->bindValue(':date', $search);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_NUM);

        } elseif (filter_var($search, FILTER_VALIDATE_EMAIL)) {
            $query = Core::query('contact-by-email');
            $query->bindValue(':coord', $search);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_NUM);

        } else {
            $search = trim($search);
            $search = preg_replace('#[^[:alpha:]]#u', '%', $search);
            $search = "%$search%";
            $query = Core::query('person-search');
            $query->bindValue(':search', $search);
            if (!$query->execute()) return false;
            return $query->fetchAll(PDO::FETCH_NUM);
        }
    }


    /**
     * Thematic searching method
     *
     * @param   string  $search     Search term
     * @result  array
     * */
    public static function search_tags($search)
    {
        $search = trim($search);
        $search = preg_replace('#[^[:alpha:]]#u', '%', $search);
        $search = "%$search%";
        $query = Core::query('person-search-tags');
        $query->bindValue(':search', $search);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_NUM);




        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');

        // On prépare le tableau global des contacts résultant de chaque recherche
        $contacts = array();

        // On prépare le terme aux like
        $terme = "%$terme%";

        // On effectue une première recherche sur les tags des fiches
        $query = $link->prepare('SELECT `contact_id` FROM `contacts` WHERE `contact_tags` LIKE :terme ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC');
        $query->bindParam(':terme', $terme);
        $query->execute();
        if ($query->rowCount() >= 1) {
            $resultats = $query->fetchAll(PDO::FETCH_NUM);
            foreach ($resultats as $resultat) { $contacts[] = $resultat[0]; }
            unset($resultats);
        }

        // On continue en vérifiant les objets d'événements
        $query = $link->prepare('SELECT `contact_id` FROM `historique` WHERE `historique_objet` LIKE :terme OR `historique_notes` LIKE :terme ORDER BY `contact_id` ASC');
        $query->bindParam(':terme', $terme);
        $query->execute();
        if ($query->rowCount() >= 1) {
            $resultats = $query->fetchAll(PDO::FETCH_NUM);
            foreach ($resultats as $resultat) { $contacts[] = $resultat[0]; }
            unset($resultats);
        }

        // On continue en vérifiant les fichiers
        $query = $link->prepare('SELECT `contact_id` FROM `fichiers` WHERE `fichier_nom` LIKE :terme OR `fichier_description` LIKE :terme ORDER BY `contact_id` ASC');
        $query->bindParam(':terme', $terme);
        $query->execute();
        if ($query->rowCount() >= 1) {
            $resultats = $query->fetchAll(PDO::FETCH_NUM);
            foreach ($resultats as $resultat) { $contacts[] = $resultat[0]; }
            unset($resultats);
        }

        // À la fin, on vérifie qu'il n'existe pas de doublons
        $contacts = array_unique($contacts);

        // On retourne la liste des contacts concernés par cette recherche
        return $contacts;
    }


    /**
     * People listing method
     *
     * @param   array   $sort       Sorting method
     * @param   int     $debut      First if int or estimation if true
     * @param   int     $nombre     Number of people or all if true
     * @result  array
     * */
    public static function listing(array $sort, $debut, $nombre = 5)
    {
        if (is_bool($debut) && $debut) {
            $query = 'SELECT COUNT(`id`) FROM `people`';
        } else {
            $query = 'SELECT `id` FROM `people`';
        }

        $conditionsStrictes = array();

        if (isset($sort['email']) && $sort['email'] == 1) {
            $_query = Core::query('person-with-emails');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` IN ('.implode(',', $ids).')';
        } elseif (isset($sort['email']) && $sort['email'] == -1) {
            $_query = Core::query('person-with-emails');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` NOT IN ('.implode(',', $ids).')';
        }

        if (isset($sort['mobile']) && $sort['mobile'] == 1) {
            $_query = Core::query('people-with-mobile');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` IN ('.implode(',', $ids).')';
        } elseif (isset($sort['mobile']) && $sort['mobile'] == -1) {
            $_query = Core::query('people-with-mobile');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` NOT IN ('.implode(',', $ids).')';
        }

        if (isset($sort['fixe']) && $sort['fixe'] == 1) {
            $_query = Core::query('people-with-fixe');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` IN ('.implode(',', $ids).')';
        } elseif (isset($sort['fixe']) && $sort['fixe'] == -1) {
            $_query = Core::query('people-with-fixe');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` NOT IN ('.implode(',', $ids).')';
        }

        if (isset($sort['phone']) && $sort['phone'] == 1) {
            $_query = Core::query('people-with-phone');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` IN ('.implode(',', $ids).')';
        } elseif (isset($sort['phone']) && $sort['phone'] == -1) {
            $_query = Core::query('people-with-phone');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` NOT IN ('.implode(',', $ids).')';
        }

        if (isset($sort['electeur']) && $sort['electeur'] == 1) {
            $conditionsStrictes[] = '`electeur` = 1';
        } elseif (isset($sort['electeur']) && $sort['electeur'] == -1) {
            $conditionsStrictes[] = '`electeur` = 0';
        }

        if (isset($sort['adresse']) && $sort['adresse'] == 1) {
            $_query = Core::query('people-with-address');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` IN ('.implode(',', $ids).')';
        } elseif (isset($sort['adresse']) && $sort['adresse'] == -1) {
            $_query = Core::query('people-with-address');
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditionsStrictes[] = '`id` NOT IN ('.implode(',', $ids).')';
        }


        $criteres = explode(';', $sort['criteres']);
        $conditions = array();
        $_conditions = array();

        if (!empty($criteres[0])) {
            foreach ($criteres as $critere) {
                $tri = explode(':', $critere);
                $_conditions[$tri[0]][] = $tri[1];
            }
        }

        if (isset($_conditions['bureau']) && count($_conditions['bureau'])) {
            $ids = implode(',', $_conditions['bureau']);
            $conditions[] = '`bureau` IN ('.$ids.')';
        }

        if (isset($_conditions['rue']) && count($_conditions['rue'])) {
            $ids = implode(',', $_conditions['rue']);
            $_query = Core::query('people-from-streets');
            $_query->bindValue(':ids', $ids);
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditions[] = '`id` IN ('.implode(',', $ids).')';
        }

        if (isset($_conditions['ville']) && count($_conditions['ville'])) {
            $ids = implode(',', $_conditions['ville']);
            $_query = Core::query('people-from-cities');
            $_query->bindValue(':ids', $ids);
            $_query->execute();
            $ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
            foreach ($result AS $element) { $ids[] = $element[0]; }
            $conditions[] = '`id` IN ('.implode(',', $ids).')';
        }

        if (isset($_conditions['thema']) && count($_conditions['thema'])) {
            $themas = array();
            foreach ($_conditions['thema'] as $tag) {
                $themas[] = '`tags` LIKE "%'.$tag.'%"';
            }

            $conditions[] = '('.implode(' OR ', $themas).')';
        }

        if (isset($_conditions['zipcode']) && count($_conditions['zipcode'])) {
            $ids = array();
            foreach ($_conditions['zipcode'] as $zip) {
                $_zipcodes = explode('&', $zip);
                $_query = Core::query('zipcodes-id-by-range');
                $_query->bindValue(':begin', $_zipcodes[0]);
                $_query->bindValue(':end', $_zipcodes[1]);
                $_query->execute();
                $zipcodes_ids = array(); $result = $_query->fetchAll(PDO::FETCH_NUM);
                foreach($result as $element) { $zipcodes_ids[] = $element[0]; }

                foreach($zipcodes_ids as $id) {
                    $__query = Core::query('people-by-zipcode');
                    $__query->bindValue(':zipcode', $id);
                    $__query->bindValue(':country', $_zipcodes[2]);
                    $__query->execute();
                    $result = $__query->fetchAll(PDO::FETCH_NUM);
                    foreach($result as $element) { $ids[] = $element[0]; }
                }

                $conditions[] = '`id` IN ('.implode(',', $ids).')';
            }
        }

        if (count($conditionsStrictes) || count($conditions)) {
            $query .= ' WHERE ';
        }

        if (count($conditionsStrictes)) {
            $conditionsStrictes = implode(' AND ', $conditionsStrictes);
            $query .= '( '.$conditionsStrictes.' )';
        }

        if (count($conditions)) {
            $conditions = implode(' AND ', $conditions);
            if (count($conditionsStrictes)) {
                $query .= ' AND ( '.$conditions.' )';
            } else {
                $query .= '( '.$conditions.' )';
            }
        }

        if (is_bool($debut) && $debut) {
            $query = Configuration::read('db.link')->prepare($query);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_NUM);
            return $result[0];

        } else {
            $query .= ' ORDER BY `nom`, `nom_usage`, `prenoms` ASC';

            if (is_bool($nombre)) {
                $query = Configuration::read('db.link')->prepare($query);
                $query->execute();
                $contacts = $query->fetchAll(PDO::FETCH_NUM);

            } else {
                $query .= ' LIMIT '.$debut.','.$nombre;
                $query = Configuration::read('db.link')->prepare($query);
                $query->execute();
                $contacts = $query->fetchAll(PDO::FETCH_NUM);
            }

            $ids = array();
            foreach ($contacts as $contact) {
                $ids[] = $contact[0];
            }
            return $ids;
        }
    }


    /**
     * Last created persons
     *
     * @param   int     $number     Asked number of created persons
     * @return  array
     * */
    public static function last($number = 5)
    {
        $link = Configuration::read('db.link');
        $query = $link->query('SELECT `id` FROM `people` ORDER BY `id` DESC LIMIT 0, ' . $number);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
