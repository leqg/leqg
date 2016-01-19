<?php
/**
 * Ajout d'un bureau dans une mission
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère les informations
if (isset($_POST['bureau'], $_POST['code'])
    || isset($_GET['bureau'], $_GET['code'])
) {
    // On récupère les données
    $bureau = (isset($_POST['bureau'])) ? $_POST['bureau'] : $_GET['bureau'];
    $code = (isset($_POST['code'])) ? $_POST['code'] : $_GET['code'];

    // On ouvre la mission
    $mission = new Mission($code);

    // On ajoute la rue
    $mission->ajoutBureau($bureau);

    // On retourne un code de réussite
    Core::tpl_go_to('mission', array('code' => $code, 'admin' => 'parcours'), true);
} else {
    // On retourne un code d'erreur
    http_response_code(418);
}
