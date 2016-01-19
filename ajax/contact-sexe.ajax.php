<?php
    $fiche = (isset($_POST['fiche'])) ? $_POST['fiche'] : 0;
    
    // On ouvre cette nouvelle fiche
    $contact = new People($fiche);
    
    // On lance le changement de sexe
    $contact->change_sex();
?>