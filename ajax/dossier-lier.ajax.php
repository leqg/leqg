<?php
/**
 * Lier un dossier à un événement
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$dossier = (isset($_GET['dossier'])) ? $_GET['dossier'] : '';
$evenement = (isset($_GET['evenement'])) ? $_GET['evenement'] : '';

// On va tout d'abord ouvrir le dossier
$dossier = new dossier(md5($dossier));

// On récupère les données en JSON
$dossier_json = $dossier->json();

// On ouvre l'événement
$evenement = new evenement(md5($evenement));

// On lie l'événement et le dossier
$evenement->linkFolder($dossier->get('dossier_id'));

// On retourne les informations sur le dossier en JSON
echo $dossier_json;
