<?php
    if (is_numeric($_POST['fiche']))
    {
        // On ouvre la fiche concernée
        $contact = new contact(md5($_POST['fiche']));
        
        // On modifie l'adresse enregistrée
        $contact->update('adresse_id', 0);
    }