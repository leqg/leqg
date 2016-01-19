<?php
/**
 * Lier deux contacts ensemble
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */


// On récupère les informations
$infos = $_POST;

// On exécute la requête
$query = 'INSERT INTO `liaisons` (`ficheA`, `ficheB`)
          VALUES (:ficheA, :ficheB)';
$query = $link->prepare($query);
$query->bindParam(':ficheA', $infos['ficheA']);
$query->bindParam(':ficheB', $infos['ficheB']);
$query->execute();
