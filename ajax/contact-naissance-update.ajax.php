<?php
	// On ouvre la fiche contact
	$fiche = md5($_POST['contact']);
	$contact = new contact($fiche);
	
	// On formate la date de naissance au bon format
	$date = explode('/', $_POST['date']);
	krsort($date);
	$date = implode('-', $date);
	
	// On met à jour la date de naissance
	$contact->update('contact_naissance_date', $date);
?>