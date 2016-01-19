<?php
/**
 * Ajout de contacts dans une mission de rappels
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On se connecte à la base de données
$link = Configuration::read('db.link');

// On récupère les informations
$var = $_GET;

// On retraite les critères complexes
$var['criteres'] = trim($var['criteres'], ';');

// On récupère la liste des contacts concernés
$contacts = People::listing($var, 0, false);

// Pour chaque contact, on l'ajoute à la mission
foreach ($contacts as $contact) {
    // On exécute la requête d'ajout du rappel
    $query = 'INSERT INTO `rappels` (`argumentaire_id`, `contact_id`)
              VALUES (:argumentaire, :contact)';
    $query = $link->prepare($query);
    $query->bindParam(':argumentaire', $var['mission'], PDO::PARAM_INT);
    $query->bindParam(':contact', $contact, PDO::PARAM_INT);
    $query->execute();
}
