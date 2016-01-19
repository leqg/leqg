<?php
    require_once 'includes.php';
    
    // Script de conversion de l'ancien système géographique au nouveau (carto => map)
    
    // On va tout d'abord rechercher une portion d'utilisateur à transférer d'une ligne à l'autre
    $query = $link->query('SELECT `contact_id`, `adresse_id`, `immeuble_id`, `contact_nom`, `contact_nom_usage`, `contact_prenoms` FROM `contacts` WHERE `living_place_id` = 0 AND `official_place_id` = 0 ORDER BY `contact_id` ASC LIMIT 0, 1000');
    $contacts = $query->fetchAll(PDO::FETCH_ASSOC);

    // Pour chaque contact, on va passer de l'ancien système au nouveau système pour les deux entrées
foreach ($contacts as $contact) {
    // On récupère d'abord les adresse actuelles
    $adresse = Carto::immeuble($contact['adresse_id']);
    $vote = Carto::immeuble($contact['immeuble_id']);
        
    // On récupère des composants pour chacune des deux
    $adresse = array(
    'numero' => trim($adresse['immeuble_numero']),
    'adresse' => trim(Carto::afficherRue($adresse['rue_id'], true)),
    'ville' => trim(Carto::afficherVille(Carto::villeParRue($adresse['rue_id']), true))
    );
    $vote = array(
    'numero' => trim($vote['immeuble_numero']),
    'adresse' => trim(Carto::afficherRue($vote['rue_id'], true)),
    'ville' => trim(Carto::afficherVille(Carto::villeParRue($vote['rue_id']), true))
    );
        
    // On récupère les informations issues du Nominatim OSM pour l'adresse déclarée
    if ($contact['adresse_id']) {
        $data = Map::geocoder($adresse);
            
        // On enregistre les informations dans la fiche du contact
        $query = $link->prepare('UPDATE `contacts` SET `living_place_id` = :place WHERE `contact_id` = :contact');
        $query->bindParam(':place', $data['place_id'], PDO::PARAM_INT);
        $query->bindParam(':contact', $contact['contact_id'], PDO::PARAM_INT);
        $query->execute();
    }
        
    // On récupère les informations issues du Nominatim OSM pour l'adresse électorale
    if ($contact['immeuble_id']) {
        $data = Map::geocoder($vote);
            
        // On enregistre les informations dans la fiche du contact
        $query = $link->prepare('UPDATE `contacts` SET `official_place_id` = :place WHERE `contact_id` = :contact');
        $query->bindParam(':place', $data['place_id'], PDO::PARAM_INT);
        $query->bindParam(':contact', $contact['contact_id'], PDO::PARAM_INT);
        $query->execute();
    }
        
    echo $contact['contact_nom'] . ' ' . $contact['contact_nom_usage'] . ' ' . $contact['contact_prenoms'] . '<br>';
}
?>