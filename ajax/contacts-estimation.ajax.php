<?php
/**
 * Estimation du nombre de contacts ciblés par un tri
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère les données envoyées par le formulaire
if (isset($_GET)) {
    // On retraite sous forme d'un tableau les données envoyées par le formulaire
    $tri = array(
    'email' => $_GET['email'],
    'mobile' => $_GET['mobile'],
    'fixe' => $_GET['fixe'],
    'electeur' => $_GET['electeur'],
    'adresse' => $_GET['adresse'],
    'criteres' => trim($_GET['criteres'], ';')
    );

    // On charge les fiches correspondantes
    $estimation = People::listing($tri, true);

    echo $estimation;
} else {
    // On retourne une erreur
    return false;
}
