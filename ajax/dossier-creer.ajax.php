<?php
    $nom = (isset($_GET['nom'])) ? $_GET['nom'] : '';
    $desc = (isset($_GET['desc'])) ? $_GET['desc'] : '';
    $event = (isset($_GET['event'])) ? $_GET['event'] : 0;
    
    // On va créer le tableau des arguments
    $args = array(
        'nom' => $nom,
        'desc' => $desc
    );
    
    // On va tout d'abord créer le dossier
    $dossier = new dossier($args, true);
    $dossier_json = $dossier->json();
    
    // On ouvre l'événement
    $evenement = new evenement(md5($event));
    
    // On lie l'événement et le dossier
    $evenement->lier_dossier($dossier->get('dossier_id'));
    
    // On retourne les informations sur le dossier en JSON
    echo $dossier_json;
?>