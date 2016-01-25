<?php
/**
 * Report an interaction in a mission
 *
 * PHP version 5
 *
 * @category Mobile
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère les informations envoyées
$infos = $_POST;

// On enregistre les informations
if ($infos['type'] == 'boitage') {
    $boitage->reporting($infos['mission'], $infos['id'], $infos['statut']);
} else if ($infos['type'] == 'porte') {
    $porte->reporting($infos['mission'], $infos['id'], $infos['statut']);
} else {
    //$boitage->reporting();
}
