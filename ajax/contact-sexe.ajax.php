<?php
/**
 * Modification du sexe d'un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$fiche = (isset($_POST['fiche'])) ? $_POST['fiche'] : 0;

// On ouvre cette nouvelle fiche
$contact = new People($fiche);

// On lance le changement de sexe
$contact->change_sex();
