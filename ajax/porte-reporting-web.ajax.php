<?php
    // On récupère les données
    if (isset($_POST['mission'], $_POST['contact'], $_POST['statut']))
    {
		$porte->reporting($_POST['mission'], $_POST['contact'], $_POST['statut']);
    }
?>