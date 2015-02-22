<?php
	// On ouvre la fiche contact
	$contact = new People($_POST['contact']);
	
	// On formate la date de naissance au bon format
	$organisme = $_POST['organisation'];
	$fonction = $_POST['fonction'];
	
	// On met à jour l'organisme
	$contact->update('organisme', $organisme);
	
	// On met à jour la fonction
	$contact->update('fonction', $fonction);
?>
