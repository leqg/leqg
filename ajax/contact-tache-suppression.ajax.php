<?php
    // On ouvre la fiche contact
    $evenement = new Event($_POST['evenement']);
    
    // On ajoute le tag
    $evenement->task_remove($_POST['tache']);
?>