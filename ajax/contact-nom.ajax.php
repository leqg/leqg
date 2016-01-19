<?php
    $fiche = (isset($_POST['fiche'])) ? $_POST['fiche'] : 0;
    $nom = (isset($_POST['nom'])) ? $_POST['nom'] : '';
    $nom_usage = (isset($_POST['nomUsage'])) ? $_POST['nomUsage'] : '';
    $prenoms = (isset($_POST['prenoms'])) ? $_POST['prenoms'] : '';
    
    // On ouvre cette nouvelle fiche
    $data = new People($fiche);
    
    // On lance le changement de sexe
    $data->update('nom', $nom);
    $data->update('nom_usage', $nom_usage);
    $data->update('prenoms', $prenoms);
?>