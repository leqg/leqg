<?php
/**
 * Suppression d'un utilisateur
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

if (isset($_GET['compte']) || isset($_POST['compte'])) {
    $compte = (isset($_GET['compte'])) ? $_GET['compte'] : $_POST['compte'];

    // On essaye de récupérer des informations
    $infos = User::data($compte);

    if ($infos) {
        $link = Configuration::read('db.core');

        $query = 'DELETE FROM `user` WHERE `id` = :id AND `client` = :client';
        $query = $link->prepare($query);
        $query->bindParam(':client', $infos['client']);
        $query->bindParam(':id', $compte);
        $query->execute();

        Core::goTo('administration', true);
    } else {
        http_response_code(418);
    }
} else {
    http_response_code(418);
}
