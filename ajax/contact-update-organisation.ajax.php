<?php
/**
 * Mise à jour de l'organisme et de la fonction d'un contact
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
$organisme = $_POST['organisation'];
$fonction = $_POST['fonction'];

// On met à jour l'organisme
$contact->update('organisme', $organisme);

// On met à jour la fonction
$contact->update('fonction', $fonction);
