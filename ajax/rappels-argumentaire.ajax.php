<?php
/**
 * Mise à jour de l'argumentaire d'une mission de rappels
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On vérifie que toutes les données ont été envoyées
if (isset($_POST['mission'], $_POST['argumentaire'])) {
    // On ouvre la mission concernée
    $mission = new Rappel($_POST['mission']);

    // On modifie les données dans la base de données
    $mission->modification('argumentaire_texte', $_POST['argumentaire']);
} else {
    return false;
}
