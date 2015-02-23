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
    private function age()
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
     * @param   array   $sort       Sorting method
     * @param   int     $debut      First if int or estimation if true
     * @param   int     $nombre     Number of people or all if true
     * @result  array
     * */
    public static function listing(array $sort, $debut, $nombre = 5)
    {
        $contacts = array(); // Contacts ciblés
        $_contacts = array(); // Contacts exclus
        
        if (is_array($sort)) {
            // We list people by contact detail sorting
            if (isset($sort['email']) && $sort['email'] != 0) {
                $query = Core::query('people-with-email');
                $query->execute();
                $contacts_with = array();
                $contacts_with_emails = $query->fetchAll(PDO::FETCH_NUM);
                foreach ($contacts_with_emails as $element) { $contacts_with[] = $element[0]; }
                
                if ($sort['email'] == 1) {
                    $contacts = array_merge($contacts, $contacts_with);
                } else {
                    $_contacts = array_merge($_contacts, $contacts_with);
                }
            }

            if (isset($sort['mobile']) && $sort['mobile'] != 0) {
                $query = Core::query('people-with-mobile');
                $query->execute();
                $contacts_with = array();
                $contacts_with_mobile = $query->fetchAll(PDO::FETCH_NUM);
                foreach ($contacts_with_mobile as $element) { $contacts_with[] = $element[0]; }
                
                if ($sort['mobile'] == 1) {
                    $contacts = array_merge($contacts, $contacts_with);
                } else {
                    $_contacts = array_merge($_contacts, $contacts_with);
                }
            }

            if (isset($sort['fixe']) && $sort['fixe'] != 0) {
                $query = Core::query('people-with-fixe');
                $query->execute();
                $contacts_with = array();
                $contacts_with_fixe = $query->fetchAll(PDO::FETCH_NUM);
                foreach ($contacts_with_fixe as $element) { $contacts_with[] = $element[0]; }
                
                if ($sort['fixe'] == 1) {
                    $contacts = array_merge($contacts, $contacts_with);
                } else {
                    $_contacts = array_merge($_contacts, $contacts_with);
                }
            }

            if (isset($sort['phone']) && $sort['phone'] != 0) {
                $query = Core::query('people-with-phone');
                $query->execute();
                $contacts_with = array();
                $contacts_with_phone = $query->fetchAll(PDO::FETCH_NUM);
                foreach ($contacts_with_phone as $element) { $contacts_with[] = $element[0]; }
                
                if ($sort['phone'] == 1) {
                    $contacts = array_merge($contacts, $contacts_with);
                } else {
                    $_contacts = array_merge($_contacts, $contacts_with);
                }
            }

            if (isset($sort['electeur']) && $sort['electeur'] != 0) {
                $status = ($sort['electeur'] == 1) ? 1 : 0;
                $query = Core::query('people-can-vote');
                $query->bindValue(':status', $status, PDO::PARAM_INT);
                $query->execute();
                $contacts_can_vote = array();
                $result = $query->fetchAll(PDO::FETCH_NUM);
                foreach ($result as $element) { $contacts_can_vote[] = $element[0]; }
                $contacts = array_merge($contacts, $contacts_can_vote);
            }
            
            if (!empty($sort['criteres'])) {
                $sorting = explode(';', $sort['criteres']);
                
                $themas = array();
                $bureaux = array();
                $rues = array();
                $villes = array();
                
                foreach ($sorting as $critere) {
                    $tri = explode(':', $sort['criteres']);
                    
                        if ($tri[0] == 'thema') { $themas[] = $tri[1]; }
                    elseif ($tri[0] == 'bureau') { $bureaux[] = $tri[1]; }
                    elseif ($tri[0] == 'rue') { $rues[] = $tri[1]; }
                    elseif ($tri[0] == 'ville') { $villes[] = $tri[1]; }
                }
                
                if (count($themas)) {
                    foreach ($themas as $thema) {
                        $thema = '%'.preg_replace('#[^[:alnum:]]#u', '%', $thema).'%';
                        $query = Core::query('people-by-tags');
                        $query->bindValue(':tag', $thema);
                        $query->execute();
                        $result = $query->fetchAll(PDO::FETCH_NUM);
                        $contacts_with = array();
                        foreach ($result as $element) { $contacts_with[] = $element[0]; }
                        $contacts = array_merge($contacts, $contacts_with);
                    }
                }
                    
                if (count($bureaux)) {
                    $ids = implode(',', $bureaux);
                    $query = Core::query('people-by-poll');
                    $query->bindValue(':polls', $ids);
                    $query->execute();
                    $result = $query->fetchAll(PDO::FETCH_NUM);
                    $contacts_with = array();
                    foreach ($result as $element) { $contacts_with[] = $element[0]; }
                    $contacts = array_merge($contacts, $contacts_with);
                }
                
                if (count($rues)) {
                    $ids = implode(',', $rues);
                    $query = Core::query('people-by-street');
                    $query->bindValue(':streets', $ids);
                    $query->execute();
                    $result = $query->fetchAll(PDO::FETCH_NUM);
                    $contacts_with = array();
                    foreach ($result as $element) { $contacts_with[] = $element[0]; }
                    $contacts = array_merge($contacts, $contacts_with);
                }
                
                if (count($villes)) {
                    $ids = implode(',', $villes);
                    $query = Core::query('people-by-city');
                    $query->bindValue(':cities', $ids);
                    $query->execute();
                    $result = $query->fetchAll(PDO::FETCH_NUM);
                    $contacts_with = array();
                    foreach ($result as $element) { $contacts_with[] = $element[0]; }
                    $contacts = array_merge($contacts, $contacts_with);
                }
            }
            
            if (count($contacts) && count($_contacts)) {
                foreach ($contacts as $key => $contact) {
                    if (in_array($contact, $_contacts)) {
                        unset($contacts[$key]);
                    }
                }
            
            } elseif (!count($contacts) && count($_contacts)) {
                $ids = implode(',', $_contacts);
                $query = Core::query('people-except');
                $query->bindValue(':ids', $ids);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_NUM);
                $contacts = array();
                foreach ($result as $element) { $contacts[] = $element[0]; }
                
            } elseif (!count($contacts) && !count($_contacts) && empty($sort['criteres'])) {
                $query = Core::query('people-all');
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_NUM);
                $contacts = array();
                foreach ($result as $element) { $contacts[] = $element[0]; }
            }
            
        }
        
        if (is_bool($debut) && $debut) {
            return count($contacts);
            
        } else {
            if (is_bool($nombre)) {
                return $contacts;
            } else {
                return array_slice($contacts, $debut, $nombre);
            }
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
