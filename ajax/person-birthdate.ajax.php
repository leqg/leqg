<?php
/**
 * Changement de la date de naissance d'un contact
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
$date = explode('/', $_POST['date']);
krsort($date);
$date = implode('-', $date);
$data->update('date_naissance', $date);
