<?php
/**
 * Add an interaction
 *
 * PHP version 5
 *
 * @category Mobile
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */


// on récupère les informations renvoyées par le formulaire
$infos = $_POST;

// On trie les informations et on formate les entrées
$fiche = $infos['fiche'];
$date = $infos['date'];
$lieu = $core->securisationString($infos['lieu']);
$objet = $core->securisationString($infos['objet']);
$notes = $core->securisationString($infos['notes']);

// On ajout l'interaction à la base de données
$enregistrement = $historique->ajout(
    $fiche,
    $_COOKIE['leqg-user'],
    $infos['type'], 
    $date,
    $lieu,
    $objet,
    $notes
);

// On affiche la fiche interaction correspondance
$core->goPage(
    'interaction',
    array('fiche' => $fiche, 'interaction' => $enregistrement),
    true
);
