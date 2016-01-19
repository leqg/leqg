<?php
/**
 * Fusion des données pour faire une nouvelle fiche
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère d'abord les informations
    $contact = $_POST['contact'];
    $fixe = $_POST['fixe'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $dateNaissance = $_POST['dateNaissance'];

// Pour chaque champ remplis, on le met à jour dans la base de données
if (!empty($fixe)) {
    $fixe = preg_replace('`[^0-9]`', '', $fixe);

    $query = 'UPDATE contacts
              SET contact_telephone = "' . $fixe . '"
              WHERE contact_id = ' . $contact;
    $db->query($query);
}

if (!empty($mobile)) {
    $mobile = preg_replace('`[^0-9]`', '', $mobile);

    $query = 'UPDATE contacts
              SET contact_mobile = "' . $mobile . '"
              WHERE contact_id = ' . $contact;
    $db->query($query);
}

if (!empty($email)) {
    $query = 'UPDATE contacts
              SET contact_email = "' . $email . '"
              WHERE contact_id = ' . $contact;
    $db->query($query);
}

if (!empty($dateNaissance)) {
    $query = 'UPDATE contacts
              SET contact_naissance_date = "' . $dateNaissance . '"
              WHERE contact_id = ' . $contact;
    $db->query($query);
}

echo 'Réussite';
