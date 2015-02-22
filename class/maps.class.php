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
     * City search method
     * 
     * @param   string  $search     Search term
     * @result  array
     * */
    public static function city_search($search)
    {
        $search = "%$search%";
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
    
}
