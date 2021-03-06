<?php
/**
 * Création d'une nouvelle mission de boîtage
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
$info = $_POST;

// On lance la création de la mission avec les informations récupérées
$mission = Boite::creation($info);

// On redirige vers la page de la mission
Core::goPage('mission', array('code' => md5($mission)), true);
