<?php
/**
 * Arrêt d'une mission en cours
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On vérifie qu'un code de mission a été entré
if (isset($_GET['code']) || isset($_POST['code'])) {
    // On récupère le code de la mission
    $code = (isset($_GET['code'])) ? $_GET['code'] : $_POST['code'];

    // On ouvre la mission
    $mission = new Mission($code);

    // On change le statut de la mission comme ouvert et on redirige
    if ($mission->fermer()) {
        Core::goPage('mission', array('code' => $code), true);
    } else {
        // En cas d'erreur, on affiche un code d'erreur
        http_response_code(418);
    }
} else {
    http_response_code(418);
}
