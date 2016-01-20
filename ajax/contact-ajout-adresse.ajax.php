<?php
/**
 * Ajout d'une nouvelle adresse à un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$rue = Maps::streetNew($_POST['rue'], $_POST['ville']);
$immeuble = Maps::buildingNew($_POST['immeuble'], $rue);
$zipcode = Maps::zipcodeNew($_POST['zipcode'], $_POST['ville']);
$adresse = Maps::addressNew(
    $_POST['fiche'],
    $_POST['ville'],
    $zipcode,
    $rue,
    $immeuble
);
$data = new People($_POST['fiche']);
$postal = $data->postal_address();
echo $postal['reel'];
