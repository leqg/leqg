<?php
/**
 * Récupération des informations d'un événement
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

if (is_string($_GET['evenement'])) {
    // On ouvre l'événement
    $event = new Evenement($_GET['evenement']);

    // On récupère les informations et on retourne le JSON
    echo $event->json_infos();
} else {
    return false;
}
