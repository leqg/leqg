<?php
/**
 * Ajout d'une rue au sein d'une mission
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
if (isset($_POST['rue'], $_POST['code']) || isset($_GET['rue'], $_GET['code'])) {
    // On récupère les données
    $rue = (isset($_POST['rue'])) ? $_POST['rue'] : $_GET['rue'];
    $code = (isset($_POST['code'])) ? $_POST['code'] : $_GET['code'];

    // On ouvre la mission
    $mission = new Mission($code);

    // On ajoute la rue
    $mission->ajoutRue($rue);

    // On retourne un code de réussite
    Core::tpl_go_to('mission', array('code' => $code, 'admin' => 'parcours'), true);
} else {
    // On retourne un code d'erreur
    http_response_code(418);
}
