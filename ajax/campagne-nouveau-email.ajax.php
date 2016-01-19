<?php
/**
 * Création d'une nouvelle campagne Email
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On lance la création de la campagne
if (isset($_GET)) {
    // On récupère les données
    $user = User::ID();

    // On crée la nouvelle mission en récupérant l'identifiant créé
    $campagne = Campaign::create("email");

    // On tâche de récupérer la liste des contacts concernés par l'envoi
    $var = $_GET;

    // On retraite les critères complexes
    $var['criteres'] = trim($var['criteres'], ';');

    // On charge les identifiants des fiches correspondantes
    $contacts = People::listing($var, 0, false);

    // On prépare la requête d'ajout des destinataires
    $query = Core::query('campagne-destinataires');

    // On enregistre les contacts concernés
    foreach ($contacts as $contact) {
        $query->bindParam(':campagne', $campagne, PDO::PARAM_INT);
        $query->bindParam(':contact', $contact, PDO::PARAM_INT);
        $query->execute();
    }

    echo $campagne;
} else {
    http_response_code(403);
}
