<?php
	// On ouvre la fiche contact
	$tache = md5($_POST['evenement']);
	$evenement = new evenement($tache);
	
	// On ajoute le tag
	$evenement->tache_suppression($_POST['tache']);
?>