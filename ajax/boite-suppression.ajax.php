<?php
/**
 * Suppression d'une opération de boîtage
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

if (isset($_POST['mission'])) {
    // On ouvre la fiche concernée
    $link = Configuration::read('db.link');

    // On effectue la modification
    $query = 'UPDATE `mission` SET `mission_statut` = 0 WHERE `mission_id` = :id';
    $query = $link->prepare($query);
    $query->bindParam(':id', $_POST['mission'], PDO::PARAM_INT);
    $query->execute();

    // On redirige vers les dossiers
    Core::goTo('boite', true);
}
