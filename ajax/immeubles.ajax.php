<?php
/**
 * Retourne les immeubles d'une rue
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

echo json_encode(Maps::streetBuildings($_GET['rue']));
