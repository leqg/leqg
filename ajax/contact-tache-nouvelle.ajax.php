<?php
/**
 * Création d'une nouvelle tâche associée à un événement d'un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On ouvre la fiche contact
$evenement = new Event($_POST['evenement']);

// On ajoute le tag
$tache[0] = $evenement->newTask(
    $_POST['user'],
    $_POST['tache'],
    $_POST['deadline']
);

if (isset($tache[0]['user'])) {
    // On récupère le nom de la fiche qui est concernée par cette tâche
    $nickname = User::getLoginByID($tache[0]['user']);
} else {
    $nickname = 'Pas d\'utilisateur attribué';
}

// On ajoute cette donnée au tableau
$tache[0]['user'] = $nickname;

echo json_encode($tache);
