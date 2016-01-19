<?php
/**
 * Modification des coordonnées d'un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On créé le lien vers la BDD
$link = Configuration::read('db.link');

if (isset($_POST['id'], $_POST['info'], $_POST['type'])) {
    // On prépare la modification
    if ($_POST['type'] == 'email') {
        $query = 'UPDATE `coordonnees`
                  SET `coordonnee_email` = :email
                  WHERE `coordonnee_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':email', $_POST['info']);
    } else {
        // On commence par retraiter le numéro de téléphone
        // avant de préparer l'enregistrement
        $numero = preg_replace('`[^0-9]`', '', $_POST['info']);

        $query = 'UPDATE `coordonnees`
                  SET `coordonnee_numero` = :numero
                  WHERE `coordonnee_id` = :id';
        $query = $link->prepare($query);
        $query->bindParam(':numero', $numero);
    }
    $query->bindParam(':id', $_POST['id']);

    // On exécute la modification
    $query->execute();
}
