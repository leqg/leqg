<?php
/**
 * Récupération des informations concernant un événement
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

if (is_numeric($_GET['evenement'])) {
    $data = new Event($_GET['evenement']);
    $json = $data->json();
    $json = utf8_encode($json);
    echo $json;
} else {
    echo '';
}
