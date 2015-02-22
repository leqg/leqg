<?php
	// On ouvre la fiche contact
	$fiche = md5($_POST['contact']);
	$contact = new contact($fiche);
	
	// On formate la date de naissance au bon format
	$organisme = $_POST['organisation'];
	$fonction = $_POST['fonction'];
	
	// On met à jour l'organisme
	$contact->update('contact_organisme', $organisme);
	
	// On met à jour la fonction
	$contact->update('contact_fonction', $fonction);
?>
