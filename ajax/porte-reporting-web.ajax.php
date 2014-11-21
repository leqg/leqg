<?php
    // On récupère les données
    if (isset($_POST['mission'], $_POST['contact'], $_POST['statut']))
    {
		Porte::reporting($_POST['mission'], $_POST['contact'], $_POST['statut']);
    }
?>