<?php
/**
 * Map & geographical methods class
 *
 * PHP version 5
 *
 * @category Map
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Map & geographical methods class
 *
 * PHP version 5
 *
 * @category Map
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Map
{
    /**
     * Get all datas for an address
     *
     * @param array $adresse target address
     *
     * @return array
     * @static
     */
    static public function geocoder(array $adresse)
    {
        // On prépare la liaison à la base de données
        $link = Configuration::read('db.link');

        // On prépare l'encodage de l'adresse
        // et de la ville pour la passer dans l'URL
        $ville = urlencode($adresse['ville']);
        $street = urlencode($adresse['numero'] . ' ' . $adresse['adresse']);

        // On prépare l'URL de récupération des données JSON
        $url = "https://nominatim.openstreetmap.org/" .
               "?format=json&city=$ville&street=$street";

        // On récupère les informations sur l'adresse au format JSON
        $json = file_get_contents($url);

        // On décode les informations reçues
        $infos = json_decode($json);

        // On récupère uniquement l'entrée qui nous intéresse
        $infos = $infos[0];

        // Au sein de ces informations, on retraite les données d'adresse complète
        $adresse = explode(',', $infos->display_name);

        // On va attribuer les données de "adresse" à "place" au fur à mesure
        // en leur donnant un nom
        $place = [
            'numero' => $adresse[0],
            'rue' => $adresse[1],
            'ville' => $adresse[4],
            'arrondissement' => $adresse[5],
            'departement' => $adresse[6],
            'region' => $adresse[7],
            'cp' => $adresse[9],
            'pays' => $adresse[10]
        ];

        // On applique un trim() à toutes les valeurs du tableau $place
        $place = array_map('trim', $place);

        // on récupère les informations qui nous intéresse
        $data = [
            'place_id' => $infos->place_id,
            'lat' => $infos->lat,
            'lng' => $infos->lon,
            'adresse' => $place,
            'type' => $infos->type,
            'licence' => $infos->licence
        ];

        // On regarde si l'immeuble existe déjà dans la base de données
        $sql = 'SELECT `street_id`
                FROM `place`
                WHERE `place_id` = :id';
        $query = $link->prepare($sql);
        $query->bindParam(':id', $data['place_id']);
        $query->execute();

        // S'il existe déjà, on récupère l'ID de la rue
        if ($query->rowCount() == 1) {
            $street = $query->fetch(PDO::FETCH_NUM);

            // On ajoute l'ID de la rue aux données
            $data['street_id'] = $street[0];
        } else {
            // On regarde d'abord sur une rue existe correspondant aux données
            $sql = 'SELECT `street_id`
                    FROM `street`
                    WHERE `street_name` = :street';
            $query = $link->prepare($sql);
            $query->bindParam(':street', $data['adresse']['rue']);
            $query->execute();

            // S'il existe une rue, on récupère son identifiant
            if ($query->rowCount() == 1) {
                $street = $query->fetch(PDO::FETCH_NUM);
                $data['street_id'] = $street[0];
            } else {
                // On récupère l'identifiant de la ville
                $sql = 'SELECT `city_id`
                        FROM `city`
                        WHERE `city_name` = :city';
                $query = $link->prepare($sql);
                $query->bindParam(':city', $data['adresse']['ville']);
                $query->execute();

                // Si une ville existe, on récupère son identifiant
                if ($query->rowCount() == 1) {
                    $city = $query->fetch(PDO::FETCH_NUM);
                    $data['city_id'] = $city[0];
                } else {
                    $sql = 'INSERT INTO `city` (`city_name`)
                            VALUES (:city)';
                    $query = $link->prepare($sql);
                    $query->bindParam(':city', $data['adresse']['ville']);
                    $query->execute();

                    // On enregistre l'identifiant de cette ville
                    $data['city_id'] = $link->lastInsertId();
                }

                // Une fois l'identifiant de la ville récupéré,
                // on enregistre cette nouvelle rue
                $sql = 'INSERT INTO `street` (`city_id`, `street_name`)
                        VALUES (:city, :street)';
                $query = $link->prepare($sql);
                $query->bindParam(':city', $data['city_id'], PDO::PARAM_INT);
                $query->bindParam(':street', $data['adresse']['rue']);
                $query->execute();

                // On enregistre l'identifiant de cette rue
                $data['street_id'] = $link->lastInsertId();
            }

            // On va transformer le numéro de l'immeuble pour en avoir
            // une version uniquement numérique
            $numeric = preg_replace('#[^0-9]#', '', $data['adresse']['numero']);

            // Une fois l'identifiant de la rue récupéré,
            // on enregistre ce nouvel immeuble
            $sql = 'INSERT INTO `place` (`place_id`,
                                         `street_id`,
                                         `place_number`,
                                         `place_number_numeric`,
                                         `place_lat`,
                                         `place_lng`,
                                         `place_type`,
                                         `place_licence`)
                    VALUES (:place,
                            :street,
                            :number,
                            :numeric,
                            :lat,
                            :lng,
                            :type,
                            :licence)';
            $query = $link->prepare($sql);
            $query->bindParam(':place', $data['place_id'], PDO::PARAM_INT);
            $query->bindParam(':street', $data['street_id'], PDO::PARAM_INT);
            $query->bindParam(':number', $data['adresse']['numero']);
            $query->bindParam(':numeric', $numeric, PDO::PARAM_INT);
            $query->bindParam(':lat', $data['lat'], PDO::PARAM_INT);
            $query->bindParam(':lng', $data['lng'], PDO::PARAM_INT);
            $query->bindParam(':type', $data['type']);
            $query->bindParam(':licence', $data['licence']);
            $query->execute();
        }

        // On retourne ces données
        return $data;
    }
}
