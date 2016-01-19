<?php

    // on récupère les informations renvoyées par le formulaire
    $infos = $_POST;
    
    // On trie les informations et on formate les entrées
    $fiche = $infos['fiche'];
    $date = $infos['date'];
    $lieu = $core->securisation_string($infos['lieu']);
    $objet = $core->securisation_string($infos['objet']);
    $notes = $core->securisation_string($infos['notes']);
    
    // On ajout l'interaction à la base de données
    $enregistrement = $historique->ajout($fiche, $_COOKIE['leqg-user'], $infos['type'], $date, $lieu, $objet, $notes);

    // On affiche la fiche interaction correspondance
    $core->tpl_go_to('interaction', array('fiche' => $fiche, 'interaction' => $enregistrement), true);
?>