<?php
/**
 * LeQG – political database manager
 * Cartographic class
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
 * Cartographic class
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
class Maps
{
    /**
     * Load country's informations
     * 
     * @param   int     $country    Country ID
     * @result  array
     * */
    public static function country_data($country)
    {
        $query = Core::query('country-data');
        $query->bindValue(':country', $country, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Country search method
     * 
     * @param   string  $search     Search term
     * @result  array
     * */
    public static function country_search($search)
    {
        $search = '%'.preg_replace('#[^A-Za-z]#', '%', $search).'%';
        $query = Core::query('country-search');
        $query->bindValue(':search', $search);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Create new country method
     * 
     * @param   string  $country    Country name
     * @result  array
     * */
    public static function country_create($country)
    {
        $query = Core::query('country-create');
        $query->bindValue(':country', $country);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * City search method
     * 
     * @param   string  $search     Search term
     * @result  array
     * */
    public static function city_search($search)
    {
        $search = '%'.preg_replace('#[^A-Za-z]#', '%', $search).'%';
        $query = Core::query('city-search');
        $query->bindValue(':search', $search);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Load city's informations
     * 
     * @param   int     $city       City ID
     * @result  array
     * */
    public static function city_data($city)
    {
        $query = Core::query('city-data');
        $query->bindValue(':city', $city, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Create new city method
     * 
     * @param   string  $city       City name
     * @param   int     $country    Country ID
     * @result  array
     * */
    public static function city_create($city, $country = null)
    {
        $query = Core::query('city-create');
        $query->bindValue(':city', $city);
        $query->bindValue(':country', $country);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * Load city's number of voters
     * 
     * @param   int     $city       City ID
     * @result  int
     * */
    public static function city_electeurs($city)
    {
        $query = Core::query('city-voters-count');
        $query->bindValue(':city', $city, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);
        return $data[0];
    }
    
    
    /**
     * Load city's number of people w/ a knowed asked type of contact detail
     * 
     * @param   int     $city       City ID
     * @param   string  $type       Contact detail type
     * @result  int
     * */
    public static function city_contact_details($city, $type)
    {
        $query = Core::query('city-contact-details');
        $query->bindValue(':city', $city, PDO::PARAM_INT);
        $query->bindValue(':type', $type);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);
        return $data[0];
    }
    
    
    /**
     * Load poll place's informations
     * 
     * @param   int     $poll       Poll ID
     * @result  array
     * */
    public static function poll_data($poll)
    {
        $query = Core::query('poll-data');
        $query->bindValue(':poll', $poll, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Poll place search method
     * 
     * @param   int     $search     Search term
     * @result  array
     * */
    public static function poll_search($search)
    {
        $search = '%'.preg_replace('#[^A-Za-z0-9]#', '%', $search).'%';
        $query = Core::query('poll-search');
        $query->bindValue(':search', $search);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Create a new poll place
     * 
     * @param   string  $number     Poll number
     * @param   string  $name       Poll name
     * @param   string  $city       Poll city
     * @result  array
     * */
    public static function poll_create($number, $name, $city = null)
    {
        $query = Core::query('poll-create');
        $query->bindValue(':number', $number);
        $query->bindValue(':name', $name);
        $query->bindValue(':city', $city);
        $query->execute();
        
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * Load street's informations
     * 
     * @param   int     $street     Street ID
     * @result  array
     * */
    public static function street_data($street)
    {
        $query = Core::query('street-data');
        $query->bindValue(':street', $street, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Create new street method
     * 
     * @param   string  $street     Street name
     * @param   int     $city       City ID
     * @result  array
     * */
    public static function street_create($street, $city = null)
    {
        $query = Core::query('street-create');
        $query->bindValue(':street', $street);
        $query->bindValue(':city', $city);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * Street search method
     * 
     * @param   string  $search     Search term
     * @result  array
     * */
    public static function street_search($search, $city = null)
    {
        if (is_null($city)) {
            $search = '%'.preg_replace('#[^A-Za-z0-9]#', '%', $search).'%';
            $query = Core::query('street-search');
            $query->bindValue(':search', $search);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $search = '%'.preg_replace('#[^A-Za-z0-9]#', '%', $search).'%';
            $query = Core::query('street-search-in-city');
            $query->bindValue(':search', $search);
            $query->bindValue(':city', $city, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    
    /**
     * List all buildings on a street
     * 
     * @param   int     $street     Asked street ID
     * @result  array
     * */
    public static function street_buildings($street)
    {
        $query = Core::query('street-buildings');
        $query->bindValue(':street', $street, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Create a new street
     * 
     * @param   string  $street     New street name
     * @param   int     $city       City ID
     * @result  int
     * */
    public static function street_new($street, $city=null)
    {
        $query = Core::query('street-new');
        $query->bindValue(':street', $street);
        $query->bindValue(':city', $city);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * Load building's informations
     * 
     * @param   int     $building   Building ID
     * @result  array
     * */
    public static function building_data($building)
    {
        $query = Core::query('building-data');
        $query->bindValue(':building', $building, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Building search method
     * 
     * @param   string  $search     Search term
     * @param   int     $street     Into a street ?
     * @result  array
     * */
    public static function building_search($search, $street = null)
    {
        if (is_null($street)) {
            $search = '%'.preg_replace('#[^A-Za-z0-9]#', '%', $search).'%';
            $query = Core::query('building-search');
            $query->bindValue(':search', $search);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $search = '%'.preg_replace('#[^A-Za-z0-9]#', '%', $search).'%';
            $query = Core::query('building-search-in-street');
            $query->bindValue(':search', $search);
            $query->bindValue(':street', $street, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    
    /**
     * Create a new building
     * 
     * @param   string  $building   New building number
     * @param   int     $street     Street ID
     * @result  int
     * */
    public static function building_new($building, $street = null)
    {
        $query = Core::query('building-new');
        $query->bindValue(':building', $building);
        $query->bindValue(':street', $street);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * Most used zipcode for a street
     * 
     * @param   int     $street     Street ID
     * @result  int
     * */
    public static function zipcode_detect($street)
    {
        $query = Core::query('zipcode-detect');
        $query->bindValue(':street', $street, PDO::PARAM_INT);
        $query->execute();
        $zipcode = $query->fetch(PDO::FETCH_NUM);
        return $zipcode[0];
    }
    
    
    /**
     * Create a new building
     * 
     * @param   string  $zipcode    New building number
     * @param   int     $city       City ID
     * @result  int
     * */
    public static function zipcode_new($zipcode, $city = null)
    {
        $query = Core::query('zipcode-new');
        $query->bindValue(':zipcode', $zipcode);
        $query->bindValue(':city', $city);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * Zipcode search method
     * 
     * @param   string  $search     Search term
     * @result  array
     * */
    public static function zipcode_search($search)
    {
        $query = Core::query('zipcode-search');
        $query->bindValue(':search', $search);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    
    /**
     * Create a new address for a contact
     * 
     * @param   int     $person     Person ID
     * @param   int     $city       City ID
     * @param   int     $zipcode    Zipcode ID
     * @param   int     $street     Street ID
     * @param   int     $building   Building ID
     * @param   string  $type       Address type
     * @result  int                 Address ID
     * */
    public static function address_new($person, $city, $zipcode, $street, $building, $type = 'reel')
    {
        $query = Core::query('address-new');
        $query->bindValue(':people', $person, PDO::PARAM_INT);
        $query->bindValue(':type', $type);
        $query->bindValue(':city', $city, PDO::PARAM_INT);
        $query->bindValue(':zipcode', $zipcode, PDO::PARAM_INT);
        $query->bindValue(':street', $street, PDO::PARAM_INT);
        $query->bindValue(':building', $building, PDO::PARAM_INT);
        $query->execute();
    }
    
}
