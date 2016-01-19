<?php
/**
 * Mise à jour de la date de naissance d'une fiche
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
$contact = new People($_POST['contact']);

// On formate la date de naissance au bon format
$date = explode('/', $_POST['date']);
krsort($date);
$date = implode('-', $date);

// On met à jour la date de naissance
$contact->update('date_naissance', $date);
