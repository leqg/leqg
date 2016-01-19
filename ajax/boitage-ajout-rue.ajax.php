<?php
/**
 * Ajout d'une rue dans une opération de boîtage
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */


// On récupère les informations
$infos = $_POST;

// On récupère des informations sur la mission
$mission = Boite::informations($infos['mission'])[0];

// On effectue l'ajout de la rue à la mission
Boite::ajoutRue($infos['rue'], $mission['mission_id']);
