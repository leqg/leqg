<?php
    // On créé la nouvelle mission et on récupère l'identifiant attribué
    $identifiant = Rappel::creer();
    
    // On redirige vers la mission créée
    Core::tpl_go_to('rappels', array('mission' => md5($identifiant)), true);