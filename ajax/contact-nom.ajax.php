<?php
	$fiche = (isset($_POST['fiche'])) ? $_POST['fiche'] : 0;
	$nom = (isset($_POST['nom'])) ? $_POST['nom'] : '';
	$nom_usage = (isset($_POST['nomUsage'])) ? $_POST['nomUsage'] : '';
	$prenoms = (isset($_POST['prenoms'])) ? $_POST['prenoms'] : '';
	
	// On ouvre cette nouvelle fiche
	$contact = new contact(md5($fiche));
	
	// On lance le changement de sexe
	$contact->modification('contact_nom', $nom);
	$contact->modification('contact_nom_usage', $nom_usage);
	$contact->modification('contact_prenoms', $prenoms);
?>