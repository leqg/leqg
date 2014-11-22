<?php
    // On vérifie que toutes les données ont été envoyées
    if (isset($_POST['mission'], $_POST['nom']))
    {
        // On ouvre la mission concernée
        $mission = new Rappel($_POST['mission']);
        
        // On modifie les données dans la base de données
        $mission->modification('argumentaire_nom', $_POST['nom']);
    }
    else
    {
        return false;
    }
?>