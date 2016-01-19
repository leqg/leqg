<?php
/**
 * Suppression d'un moyen de contact pour un contact
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
$dsn = 'mysql:host=' . Configuration::read('db.host') .
       ';dbname=' . Configuration::read('db.basename');
$user = Configuration::read('db.user');
$pass = Configuration::read('db.pass');

$link = new PDO($dsn, $user, $pass);

if (isset($_POST['id'])) {
    // On recherche des informations sur le type de coordonnées
    // et le contact concerné
    $query = 'SELECT `contact_id`, `coordonnee_type`
              FROM `coordonnees`
              WHERE `coordonnee_id` = :id';
    $query = $link->prepare($query);
    $query->bindParam(':id', $_POST['id']);
    $query->execute();
    $infos = $query->fetch(PDO::FETCH_NUM);

    // On supprime maintenant la coordonnée
    $query = 'DELETE FROM `coordonnees`
              WHERE `coordonnee_id` = :id';
    $query = $link->prepare($query);
    $query->bindParam(':id', $_POST['id']);
    $query->execute();

    // On retire une coordonnée dans l'enregistrement du contact
    $query = 'UPDATE `contacts`
              SET `contact_' . $infos[1] . '` = `contact_' . $infos[1] . '` - 1
              WHERE `contact_id` = :id';
    $query = $link->prepare($query);
    $query->bindParam(':id', $infos[0]);
    $query->execute();
}
