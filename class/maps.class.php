<?php
/**
 * Maps class
 *
 * PHP version 5
 *
 * @category Maps
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Maps class
 *
 * PHP version 5
 *
 * @category Maps
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Maps
{
    /**
     * Load country's informations
     *
     * @param integer $country Country ID
     *
     * @return array
     * @static
     */
    static public function countryData(int $country)
    {
        $query = Core::query('country-data');
        $query->bindValue(':country', $country, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Country search method
     *
     * @param string $search Search term
     *
     * @return array
     * @static
     */
    static public function countrySearch(string $search)
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
     * @param string $country Country name
     *
     * @return array
     * @static
     */
    static public function countryCreate(string $country)
    {
        $query = Core::query('country-create');
        $query->bindValue(':country', $country);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }

    /**
     * City search method
     *
     * @param string $search Search term
     *
     * @return array
     * @static
     */
    static public function citySearch(string $search)
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
     * @param integer $city City ID
     *
     * @return array
     * @static
     */
    static public function cityData(int $city)
    {
        $query = Core::query('city-data');
        $query->bindValue(':city', $city, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new city method
     *
     * @param string  $city    City name
     * @param integer $country Country ID
     *
     * @return array
     * @static
     */
    static public function cityCreate(string $city, $country = null)
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
     * @param integer $city City ID
     *
     * @return integer
     * @static
     */
    static public function cityVoters(int $city)
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
     * @param integer $city City ID
     * @param string  $type Contact detail type
     *
     * @return integer
     * @static
     */
    static public function cityContactDetails(int $city, string $type)
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
     * @param integer $poll Poll ID
     *
     * @return array
     * @static
     */
    static public function pollData(array $poll)
    {
        $query = Core::query('poll-data');
        $query->bindValue(':poll', $poll, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Poll place search method
     *
     * @param integer $search Search term
     *
     * @return array
     * @static
     */
    static public function pollSearch(int $search)
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
     * @param string $number Poll number
     * @param string $name   Poll name
     * @param string $city   Poll city
     *
     * @return array
     * @static
     */
    static public function pollCreate(string $number, string $name, $city = null)
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
     * @param integer $street Street ID
     *
     * @return array
     * @static
     */
    static public function streetData(int $street)
    {
        $query = Core::query('street-data');
        $query->bindValue(':street', $street, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new street method
     *
     * @param string  $street Street name
     * @param integer $city   City ID
     *
     * @return array
     * @static
     */
    static public function streetCreate(string $street, $city = null)
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
     * @param string  $search Search term
     * @param integer $city   City ID
     *
     * @return array
     * @static
     */
    static public function streetSearch(string $search, $city = null)
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
     * @param integer $street Asked street ID
     *
     * @return array
     * @static
     */
    static public function streetBuildings(int $street)
    {
        $query = Core::query('street-buildings');
        $query->bindValue(':street', $street, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new street
     *
     * @param string  $street New street name
     * @param integer $city   City ID
     *
     * @return integer
     * @static
     */
    static public function streetNew(string $street, $city = null)
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
     * @param integer $building Building ID
     *
     * @return array
     * @static
     */
    static public function buildingData(int $building)
    {
        $query = Core::query('building-data');
        $query->bindValue(':building', $building, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Building search method
     *
     * @param string  $search Search term
     * @param integer $street Into a street?
     *
     * @return array
     * @static
     */
    static public function buildingSearch(string $search, $street = null)
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
     * @param string  $building New building number
     * @param integer $street   Street ID
     *
     * @return integer
     * @static
     */
    static public function buildingNew(string $building, $street = null)
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
     * @param integer $street Street ID
     *
     * @return integer
     * @static
     */
    static public function zipcodeDetect(int $street)
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
     * @param string  $zipcode New building number
     * @param integer $city    City ID
     *
     * @return integer
     * @static
     * */
    static public function zipcodeNew(string $zipcode, $city = null)
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
     * @param string $search Search term
     *
     * @return array
     * @static
     */
    static public function zipcodeSearch(string $search)
    {
        $query = Core::query('zipcode-search');
        $query->bindValue(':search', $search);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new address for a contact
     *
     * @param integer $person   Person ID
     * @param integer $city     City ID
     * @param integer $zipcode  Zipcode ID
     * @param integer $street   Street ID
     * @param integer $building Building ID
     * @param string  $type     Address type
     *
     * @return integer
     * @static
     */
    static public function addressNew(
        int $person,
        int $city,
        int $zipcode,
        int $street,
        int $building,
        $type = 'reel'
    ) {
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
