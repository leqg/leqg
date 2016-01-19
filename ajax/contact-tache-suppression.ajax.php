<?php
/**
 * Suppression d'une tâche associée à un événement associé à un contact
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
$evenement->task_remove($_POST['tache']);
