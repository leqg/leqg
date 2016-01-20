<?php
/**
 * Suppression d'un compte utilisateur
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère l'identifiant de la fiche à supprimer
$id = $_GET['id'];

// S'il s'agit bien d'une fiche, on lance la méthode de suppression des fiches
if (is_numeric($id)) {
    $user->suppression($id); 
}

// On retourne vers la liste des comptes
$core->goTo('administration', true);
