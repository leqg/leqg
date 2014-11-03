<?php
    if (isset($_POST['fiche']))
    {
        // On ouvre la fiche concernée
        $contact = new Contact(md5($_POST['fiche']));
        
        // On détruit ce contact
        $contact->destruction();
        
        // On redirige vers les dossiers
        Core::tpl_go_to('dossier', true);
    }
?>