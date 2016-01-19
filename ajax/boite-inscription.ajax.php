<?php
/**
 * Inscription d'un membre à une mission de boîtage
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On lance la connexion
$link = Configuration::read('db.link');

// On réalise l'inscription
if (isset($_POST['mission'])) {
    $userId = User::ID();

    $query = 'INSERT INTO `inscriptions` (`mission_id`, `user_id`) ';
    $query .= 'VALUES (:mission, :user)');
    $query = $link->prepare($query);
    $query->bindParam(':mission', $_POST['mission'], PDO::PARAM_INT);
    $query->bindParam('user', $userId, PDO::PARAM_INT);
    $query->execute();
}
