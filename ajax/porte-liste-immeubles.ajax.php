<?php
/**
 * Liste des immeubles d'une mission de porte à porte
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$infos = $_GET;

// On récupère des informations sur la mission
$mission = Porte::informations($infos['mission'])[0];

// On récupère les rues de la mission avec leurs immeubles
$rues = Porte::liste($mission['mission_id'], 0);

// On récupère les immeubles à faire de notre rue
$immeubles = $rues[$infos['rue']];

// Pour chaque immeuble, on modifie l'ID en son numéro
foreach ($immeubles as $key => $immeuble) {
    $i = Carto::immeuble($immeuble);
    $immeubles[$key] = $i['immeuble_numero'];
}

// On tri les résultats
natsort($immeubles);
$liste = array();

foreach ($immeubles as $i) {
    $liste[] = $i;
}

// On exporte le tout en JSON
echo json_encode($liste);
