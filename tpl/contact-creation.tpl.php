<?php
    // On va commencer par créer une nouvelle fiche et récupérer son identifiant
    $id = Contact::creation();
    
    // On redirige vers la nouvelle fiche créée
    Core::tpl_go_to('contact', array('contact' => md5($id)), true);
?>