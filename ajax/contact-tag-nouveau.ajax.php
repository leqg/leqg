<?php
/**
 * Création d'un nouveau tag associé au contact
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
if (!empty($_POST['tag'])) {
    $data->tag_add($_POST['tag']);
}
