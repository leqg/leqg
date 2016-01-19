<?php
/**
 * On récupère les fiches à contacter d'une mission de rappels
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On fait le lien à la BDD
$link = Configuration::read('db.link');

// On récupère les informations sur la mission
if (isset($_GET['mission'])) {

    // On récupère la liste des rappels fait ou à effectuer
    $query = 'SELECT `contact_id`
              FROM `rappels`
              WHERE `argumentaire_id` = :mission';
    $query = $link->prepare($query);
    $query->bindParam(':mission', $_GET['mission']);
    $query->execute();
    $contacts = $query->fetchAll(PDO::FETCH_ASSOC);

    // On récupère les informations sur les fiches
    $fiches = array();
    foreach ($contacts as $contact) {
        $fiche = new People($contact['contact_id']);
        $fiches[$fiche->get('contact_id')] = $fiche->data();
    }

    // On retourne toutes les informations sur les fiches trouvées
    echo json_encode($fiches);
}
