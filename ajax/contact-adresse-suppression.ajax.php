<?php
/**
 * Suppression de l'adresse d'un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

if (is_numeric($_POST['fiche'])) {
    // On ouvre la fiche concernée
    $contact = new contact(md5($_POST['fiche']));

    // On modifie l'adresse enregistrée
    $contact->update('adresse_id', 0);
}
