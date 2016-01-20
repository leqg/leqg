<?php

    // on récupère les informations renvoyées par le formulaire
    $infos = $_POST;
    
    // On trie les informations et on formate les entrées
    $fiche = $infos['fiche'];
    $date = $infos['date'];
    $lieu = $core->securisationString($infos['lieu']);
    $objet = $core->securisationString($infos['objet']);
    $notes = $core->securisationString($infos['notes']);
    
    // On ajout l'interaction à la base de données
    $enregistrement = $historique->ajout($fiche, $_COOKIE['leqg-user'], $infos['type'], $date, $lieu, $objet, $notes);

    // On affiche la fiche interaction correspondance
    $core->goTo('interaction', array('fiche' => $fiche, 'interaction' => $enregistrement), true);
?>