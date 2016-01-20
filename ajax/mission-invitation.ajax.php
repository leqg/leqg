<?php
/**
 * Invitation d'un membre à une mission
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On vérifie qu'un code de mission a été entré
if ((isset($_GET['code']) || isset($_POST['code']))
    && (isset($_GET['user']) || isset($_POST['user']))
) {
    // On récupère le code de la mission
    $code = (isset($_GET['code'])) ? $_GET['code'] : $_POST['code'];
    $user = (isset($_GET['user'])) ? $_GET['user'] : $_POST['user'];

    // On ouvre la mission
    $mission = new Mission($code);

    // On invite la personne demandée
    if ($mission->invitation($user)) {
        Core::goPage(
            'mission',
            array('code' => $code, 'admin' => 'invitations'),
            true
        );
    } else {
        http_response_code(418);
    }
} else {
    http_response_code(418);
}
?>
