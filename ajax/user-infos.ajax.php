<?php
/**
 * Récupération d'informations concernant un utilisateur
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

header('Content-Type: application/json');

// On récupère l'ID du compte dont nous devons chercher les informations
if (is_numeric($_GET['user'])) {
    $id = $_GET['user'];
} else {
    $id = null;
}

if (!is_null($id)) {
    $infos = $user->infos_publiques($id);
    echo json_encode($infos);
}
