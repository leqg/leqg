<?php
    if (isset($_POST['fiche']))
    {
        // On ouvre la fiche concernée
        $contact = new People($_POST['fiche']);
        
        // On détruit ce contact
        $contact->delete();
        
        // On redirige vers les dossiers
        Core::tpl_go_to('contacts', true);
    }
?>