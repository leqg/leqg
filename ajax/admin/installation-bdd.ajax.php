<?php
/**
 * Installation de la base de données sur la base du squelette
 *
 * PHP version 5
 *
 * @category Installation
 * @package  Installation
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère le contenu du fichier comme requête SQL
$sql = file_get_contents('squelette.sql');

// On extrait toutes les requêtes SQL
$req = explode(';', $sql);

// On initialise la gestion des erreurs
$erreurs = array();

// On exécute toutes les requêtes
foreach ($req as $r) {
    $db->query($r);
}
