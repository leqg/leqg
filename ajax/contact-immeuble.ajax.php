<?php
/**
 * Modification de l'immeuble d'habitation d'un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$street = Maps::street_data($_POST['rue']);
$building = Maps::building_new($_POST['immeuble'], $street['id']);
$zipcode = Maps::zipcode_detect($street['id']);
$city = Maps::city_data($street['city']);
$address = Maps::address_new(
    $_POST['fiche'],
    $city['id'],
    $zipcode,
    $street['id'],
    $building
);
$data = new People($_POST['fiche']);
$postal = $data->postal_address();
echo $postal['reel'];
