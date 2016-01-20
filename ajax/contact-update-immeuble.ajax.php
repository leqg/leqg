<?php
/**
 * Mise à jour de l'immeuble associé à un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$building = Maps::buildingData($_POST['immeuble']);
$street = Maps::streetData($building['street']);
$zipcode = Maps::zipcodeDetect($street['id']);
$city = Maps::cityData($street['city']);
$address = Maps::addressNew(
    $_POST['contact'],
    $city['id'],
    $zipcode,
    $street['id'],
    $building['id']
);

$data = new People($_POST['contact']);
$postal = $data->postal_address();
echo $postal['reel'];
