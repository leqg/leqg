<?php
/**
 * Estimation du nombre de contacts selon un tri
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

if (isset($_GET)) {
    // On retraite sous forme d'un tableau les données envoyées par le formulaire
    $tri = array(
        'email' => (isset($_GET['email'])) ? $_GET['email'] : '',
        'mobile' => (isset($_GET['mobile'])) ? $_GET['mobile'] : '',
        'fixe' => (isset($_GET['fixe'])) ? $_GET['fixe'] : '',
        'phone' => (isset($_GET['phone'])) ? $_GET['phone'] : '',
        'electeur' => (isset($_GET['electeur'])) ? $_GET['electeur'] : '',
        'adresse' => (isset($_GET['adresse'])) ? $_GET['adresse'] : '',
        'criteres' => (isset($_GET['criteres'])) ? trim($_GET['criteres'], ';') : ''
    );

    // On charge les fiches correspondantes
    $estimation = People::listing($tri, true);

    echo $estimation;
} else {
    // On retourne une erreur
    return false;
}
