<?php
/**
 * Ajout d'une rue dans une mission de porte à porte
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
if (isset($_POST['rue'], $_POST['mission'])
    || isset($_GET['rue'], $_GET['mission'])
) {
    // On récupère les données
    $rue = (isset($_POST['rue'])) ? $_POST['rue'] : $_GET['rue'];
    $mission = (isset($_POST['mission'])) ? $_POST['mission'] : $_GET['mission'];

    // On ouvre la mission
    $mission = new Mission($mission);

    // On ajoute la rue
    $mission->ajoutRue($rue);

    // On retourne un code de réussite
    http_response_code(200);
} else {
    // On retourne un code d'erreur
    http_response_code(418);
}

?>
