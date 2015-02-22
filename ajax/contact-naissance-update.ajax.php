<?php
	// On ouvre la fiche contact
	$contact = new People($_POST['contact']);
	
	// On formate la date de naissance au bon format
	$date = explode('/', $_POST['date']);
	krsort($date);
	$date = implode('-', $date);
	
	// On met à jour la date de naissance
	$contact->update('date_naissance', $date);
?>