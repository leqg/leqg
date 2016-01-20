<?php
/**
 * Suppression d'un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

if (isset($_POST['fiche'])) {
    // On ouvre la fiche concernée
    $contact = new People($_POST['fiche']);

    // On détruit ce contact
    $contact->delete();

    // On redirige vers les dossiers
    Core::goPage('contacts', true);
}
