<?php
/**
 * Créer un nouveau dossier
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$name = (isset($_GET['nom'])) ? $_GET['nom'] : '';
$desc = (isset($_GET['desc'])) ? $_GET['desc'] : '';
$event = (isset($_GET['event'])) ? $_GET['event'] : 0;

$dossier = Folder::create($name, $desc);
$dossier = new Folder($dossier);
$event = new Event($event);
$event->link_folder($dossier->get('id'));

echo $dossier->json();
