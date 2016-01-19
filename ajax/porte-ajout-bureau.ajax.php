<?php

    // On récupère les informations
    $infos = $_POST;
    
    // On récupère des informations sur la mission
    $mission = Porte::informations($infos['mission'])[0];
    
    // On effectue l'ajout de la rue à la mission
    Porte::ajoutBureau($infos['bureau'], $mission['mission_id']);
    
?>