<?php
/**
 * Mise à jour des informations d'un événement
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On regarde si on a bien les informations nécessaires
if (isset($_POST['evenement'], $_POST['info'], $_POST['value'])) {
    $event = new Event($_POST['evenement']);
    $event->update($_POST['info'], $_POST['value']);
} else {
    return false;
}
