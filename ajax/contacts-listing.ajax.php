<?php
/**
 * Extrait une liste de contacts selon un tri demandé
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
    $tri = [
        'email' => $_GET['email'],
        'mobile' => $_GET['mobile'],
        'fixe' => $_GET['fixe'],
        'electeur' => $_GET['electeur'],
        'adresse' => $_GET['adresse'],
        'criteres' => trim($_GET['criteres'], ';')
    ];

    if (isset($_GET['phone'])) {
        $tri['phone'] = $_GET['phone'];
    } else {
        $tri['phone'] = 0;
    }

    // On charge les fiches correspondantes
    $contacts = People::listing($tri, $_GET['debut']);

    // On prépare l'array de résultat
    $fiches = array();

    // Pour chaque identifiant trouvé,
    // on cherche l'ensemble des données afférentes
    foreach ($contacts as $c) {
        $contact = new Contact(md5($c));
        $fiches[$c] = $contact->donnees();
    }

    // On transforme le tableau final en JSON
    $json = json_encode($fiches);

    // On retourne le tableau
    echo $json;
} else {
    // On retourne une erreur
    return false;
}
