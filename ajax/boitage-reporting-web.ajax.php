<?php
    // On récupère les données
    if (isset($_POST['mission'], $_POST['immeuble'], $_POST['statut']))
    {
		$boitage->reporting($_POST['mission'], $_POST['immeuble'], $_POST['statut']);
    }
?>