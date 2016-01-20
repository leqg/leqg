<?php
    // On créé la nouvelle mission et on récupère l'identifiant attribué
    $identifiant = Rappel::creer();
    
    // On redirige vers la mission créée
    Core::goTo('rappels', array('mission' => $identifiant), true);
?>