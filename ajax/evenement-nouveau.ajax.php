<?php
/**
 * Création d'un nouvel événement
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$event = Event::create($_GET['contact']);
$event = new Event($event);
echo $event->json();
