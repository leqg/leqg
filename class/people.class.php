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
        if (!empty($this->_people['nom'])) $display[] = strtoupper($this->_people['nom']);
        if (!empty($this->_people['nom_usage'])) $display[] = strtoupper($this->_people['nom_usage']);
        if (!empty($this->_people['prenoms'])) $display[] = ucwords(strtolower($this->_people['prenoms']));
        
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
     * Return person city
     * 
     * @result  string
     * */
    public function city()
    {
        if (!empty($postal['officiel']['city'])) {
            return $postal['officiel']['city'];
        } elseif (!empty($postal['reel']['city'])) {
            return $postal['reel']['city'];
        } else {
            return 'Aucune ville connue';
        }
    }
    
    
    /**
     * Return postal address
     * 
     * @result  string
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
     * @result  void
     * */
    public function contact_details_add($detail)
    {
        if (filter_var($detail, FILTER_VALIDATE_EMAIL)) {
            $query = Core::query('contact-details-add-email');
            $type = "email";
        } else {
            $detail = preg_replace('#[^0-9]#', '', $detail);
            $indicatif = substr($detail, 0, 2);
            if ($indicatif == "06" && $indicatif == "07") {
                $type = "mobile";
            } else {
                $type = "fixe";
            }
            $query = Core::query('contact-details-add-phone');
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
        
        if (array_search($tag, $tags)) {
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
        $birthdate = DateTime::createFromFormat('d/m/Y', $recherche);
        
        if ($birthdate) {
            $search = $date->format('Y-m-d');
            $query = Core::query('person-search-birthdate');
            $query->bindValue(':date', $search);
            if (!$query->execute()) return false;
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } else {
            $search = trim($search);
            $search = preg_replace('#[^[:alpha:]]#u', '%', $search);
            $search = "%$search%";
            $query = Core::query('person-search');
            $query->bindValue(':search', $search);
            if (!$query->execute()) return false;
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    
    /**
     * People listing method
     * 
     * @param   array   $tri        Sorting method
     * @param   int     $debut      First
     * @param   int     $nombre     Number of people
     * @result  array
     * */
    public static function listing(array $tri, $debut, $nombre = 5)
    {
        if ((is_numeric($debut) || is_bool($debut)) && (is_numeric($nombre) || is_bool($nombre)) && is_array($tri)) {
            $link = Configuration::read('db.link');

            if (is_bool($debut) && $debut === true) {
                // On commence par préparer le visage de la requête de recherche
                $sql = 'SELECT COUNT(`contact_id`) AS `nombre` FROM `contacts` ';
            } else {
                // On commence par préparer le visage de la requête de recherche
                $sql = 'SELECT `contact_id` FROM `contacts` ';
            }
            // On va chercher à y ajouter les différents critères, à travers un tableau $criteres
            $criteres = array();
            
                // On retraite le critère "email" (si 1 (non) ou 2 (oui) on fait -1 pour obtenir le booléen souhaité pour la BDD
                if ($tri['email']) {
                    // Si le tri demandé concerne les fiches avec l'information
                    if ($tri['email'] == 2) { $criteres[] = '`contact_email` > 0'; }
                    // Sinon, s'il concerne les fiches sans l'information
                    else { $criteres[] = '`contact_email` = 0'; }
                }
                
                if ($tri['adresse']) {
                    // Si le tri demandé concerne les fiches avec forcément une adresse
                    if ($tri['adresse'] == 2) { $criteres[] = '(`adresse_id` != 0 OR `immeuble_id` != 0)'; }
                    // Sinon s'il concerne les fiches sans l'information adresse
                    else { $criteres[] = '(`adresse_id` = 0 AND `immeuble_id` = 0)'; }
                }
            
                // On retraite le critère "mobile" (si 1 (non) ou 2 (oui) on fait -1 pour obtenir le booléen souhaité pour la BDD
                if ($tri['mobile']) {
                    // Si le tri demandé concerne les fiches avec l'information
                    if ($tri['mobile'] == 2) { $criteres[] = '`contact_mobile` > 0'; }
                    // Sinon, s'il concerne les fiches sans l'information
                    else { $criteres[] = '`contact_mobile` = 0'; }
                }
            
                // On retraite le critère "fixe" (si 1 (non) ou 2 (oui) on fait -1 pour obtenir le booléen souhaité pour la BDD
                if ($tri['fixe']) {
                    // Si le tri demandé concerne les fiches avec l'information
                    if ($tri['fixe'] == 2) { $criteres[] = '`contact_fixe` > 0'; }
                    // Sinon, s'il concerne les fiches sans l'information
                    else { $criteres[] = '`contact_fixe` = 0'; }
                }
                
                // On retraite le critère "phone" (si 1 (non) ou 2 (oui) on fait -1 pour obtenir le booléen souhaité pour la BDD
                if (isset($tri['phone']) && $tri['phone']) {
                    // Si le tri demandé concerne les fiches avec l'information
                    if ($tri['phone'] == 2) { $criteres[] = '( `contact_fixe` > 0 OR `contact_mobile` > 0 )'; }
                }
            
                // On retraite le critère "electeur" (si 1 (non) ou 2 (oui) on fait -1 pour obtenir le booléen souhaité pour la BDD
                if ($tri['electeur']) { $criteres[] = '`contact_electeur` = ' . ($tri['electeur'] - 1); }
                
                // Si des critères plus complexes sont demandés, on s'en occupe ici
                if (!empty($tri['criteres'])) {
                    $criteria = explode(';', $tri['criteres']);
                    
                    
                    // On prépare les tableaux avec les différents critères
                    $themas = array();
                    $birth = array();
                    $bureaux = array();
                    $rues = array();
                    $votes = array();
                    
                    
                    // On sépare les critères de leurs type
                    foreach ($criteria as $key => $val) {
                        $crit = explode(':', $val);
                        
                        if ($crit[0] == 'thema') { $themas[] = $crit[1]; }
                        else if ($crit[0] == 'birth') { $birth[] = $crit[1]; }
                        else if ($crit[0] == 'bureau') { $bureaux[] = $crit[1]; }
                        else if ($crit[0] == 'rue') { $rues[] = $crit[1]; }
                        else if ($crit[0] == 'vote') { $votes[] = $crit[1]; }
                    }
                    
                    // On va analyser les critères thématiques pour les ajouter à la condition SQL
                    if (count($themas)) {
                        // On va ajouter chaque condition thématique à la recherche
                        foreach ($themas as $thema) { 
                            $thema = preg_replace('#[^[:alnum:]]#u', '%', $thema);
                            $criteres[] = '`contact_tags` LIKE "%' . $thema . '%"';
                        }
                    }
                    
                    // On va analyser les critères de votes pour les ajouter à la condition SQL
                    if (count($votes)) {
                        foreach ($votes as $vote) {
                            $criteres[] = '`contact_vote_' . $vote . '` = 1';
                        }
                    }
                    
                    // On va analyser les critères de naissance pour les ajouter à la condition SQL
                    if (count($birth)) {
                        // On va ajouter chaque condition de naissance à la recherche $dates en retraitant son format
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
                            $criteres[] = '(`contact_naissance_date` = "' . implode('" OR `contact_naissance_date` = "') . '")';
                        }
                    }
                    
                    // On va analyser les bureaux de votes demandés pour extraire tous les électeurs au sein de ceux-ci
                    if (count($bureaux)) {
                        // On prépare la liste des bureaux de vote pour l'insérer dans la requête
                        $ids = implode(',', $bureaux);
                        
                        // On prépare la condition SQL
                        $criteres[] = '`bureau_id` IN (' . $ids . ')';
                    }
                    
                    
                    // On va analyser toutes les rues demandées pour récupérer tous les ID d'immeubles concernées par ces rues et les électeurs qui y sont
                    if (count($rues)) {
                        // Pour chaque rue, on cherche les immeubles concernés
                        $immeubles = array();
                        foreach ($rues as $rue) {
                            $query = $link->prepare('SELECT `immeuble_id` FROM `immeubles` WHERE `rue_id` = :id');
                            $query->bindParam(':id', $rue);
                            $query->execute();
                            $ids = $query->fetchAll(PDO::FETCH_NUM);
                            
                            // Pour chaque immeuble trouvé, on le rajoute dans le tableau $immeubles
                            foreach ($ids as $id) { $immeubles[] = $id[0]; }
                        }
                        
                        // On transforme cette liste d'immeuble pour l'intégrer dans la requête SQL
                        $ids = implode(',', $immeubles);
                        
                        // On rajoute la requête aux conditions SQL
                        $criteres[] = '( `immeuble_id` IN (' . $ids . ') OR `adresse_id` IN (' . $ids . ') )';
                    }
                }
            
            
            // On retraite les critères en conditions SQL
            if ($criteres) {
                $sql.= ' WHERE ' . implode(' AND ', $criteres);
            }
            
            // On ajoute les conditions de nombre et d'ordre
            if ($nombre && !is_bool($debut)) {
                $sql.= ' ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC LIMIT ' . $debut . ', ' . $nombre;
            } else {
                $sql.= ' ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC';
            }
            // On exécute la requête SQL
            $query = $link->prepare($sql);
            $query->execute();
            
            // Si on souhaite uniquement une estimation, on retourne le nombre
            if (is_bool($debut) && $debut === true) {
                $nombre = $query->fetch(PDO::FETCH_NUM);
                return $nombre[0];
            }
            // Sinon, on retourne la liste des identifiants
            else {
                // On retraite la liste des identifiants pour en faire un tableau PHP $contacts
                $ids = $query->fetchAll(PDO::FETCH_NUM);
                $contacts = array();
                foreach ($ids as $id) $contacts[] = $id[0];
                // On retourne la liste des ids de fiches concernées par la requête
                return $contacts;
            }
                     
        } else {
            // On retourne une erreur
            return false;
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
