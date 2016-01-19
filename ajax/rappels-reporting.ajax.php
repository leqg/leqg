<?php
/**
 * On effectue le reporting d'un appel d'une mission
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On sauvegarde les données envoyées
if (isset($_GET['notes'], $_GET['contact'], $_GET['argumentaire'])) {
    // On se connecte à la BDD
    $link = Configuration::read('db.link');

    // On exécute la requête de sauvegarde
    $query = 'UPDATE `rappels`
              SET `rappel_reporting` = :notes
              WHERE `contact_id` = :contact
              AND `argumentaire_id` = :argumentaire';
    $query = $link->prepare($query);
    $query->bindParam(':notes', $_GET['notes']);
    $query->bindParam(':contact', $_GET['contact'], PDO::PARAM_INT);
    $query->bindParam(':argumentaire', $_GET['argumentaire'], PDO::PARAM_INT);
    $query->execute();
}
