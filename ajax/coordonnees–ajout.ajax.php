<?php
/**
 * Ajout d'une coordonnées postale à un contact
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$data = new People($_POST['contact']);
$data->contact_details_add($_POST['coordonnees'], $_POST['type']);
