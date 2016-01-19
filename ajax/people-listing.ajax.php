<?php
/**
 * Retourne une liste des contacts selon un tri demandé
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
        'email' => (isset($_GET['email'])) ? $_GET['email'] : '',
        'mobile' => (isset($_GET['mobile'])) ? $_GET['mobile'] : '',
        'fixe' => (isset($_GET['fixe'])) ? $_GET['fixe'] : '',
        'phone' => (isset($_GET['phone'])) ? $_GET['phone'] : '',
        'electeur' => (isset($_GET['electeur'])) ? $_GET['electeur'] : '',
        'adresse' => (isset($_GET['adresse'])) ? $_GET['adresse'] : '',
        'criteres' => (isset($_GET['criteres'])) ? trim($_GET['criteres'], ';') : ''
    );

    // On charge les fiches correspondantes
    $contacts = People::listing($tri, $_GET['debut']);

    // On prépare l'array de résultat
    $fiches = array();

    // Pour chaque identifiant trouvé,
    // on cherche l'ensemble des données afférentes
    foreach ($contacts as $c) {
        $contact = new People($c);
        $fiches[$c] = $contact->data();
    }

    // On transforme le tableau final en JSON
    $json = json_encode($fiches);

    // On retourne le tableau
    echo $json;
} else {
    // On retourne une erreur
    return false;
}
