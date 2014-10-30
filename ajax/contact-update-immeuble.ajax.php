<?php
	// On ouvre la fiche contact
	$fiche = md5($_POST['contact']);
	$contact = new contact($fiche);
	
	// On récupère l'identifiant de l'immeuble
	$immeuble = $_POST['immeuble'];
	
	// On met à jour l'organisme
	$contact->update('adresse_id', $immeuble);
	
	// On récupère l'adresse postale correspondante à cet immeuble
	echo $contact->adresse('declaree');
?>
