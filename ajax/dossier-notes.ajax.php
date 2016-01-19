<?php
/**
 * Mise à jour de la note globale associée au dossier
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$dossier = new Folder($_POST['dossier']);
$dossier->update('dossier_notes', $_POST['notes']);
