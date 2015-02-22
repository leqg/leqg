<?php
	// On ouvre la fiche contact
	$fiche = md5($_POST['contact']);
	$contact = new contact($fiche);
	
	// On ajoute le tag
	$contact->tag_ajout($_POST['tag']);
?>