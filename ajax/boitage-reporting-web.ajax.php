<?php
    // On récupère les données
    if (isset($_POST['mission'], $_POST['immeuble'], $_POST['statut']))
    {
		Boite::reporting($_POST['mission'], $_POST['immeuble'], $_POST['statut']);
    }
?>