<?php
	$fiche = (isset($_POST['fiche'])) ? $_POST['fiche'] : 0;
	
	// On ouvre cette nouvelle fiche
	$contact = new contact(md5($fiche));
	
	// On lance le changement de sexe
	$contact->changement_sexe();
?>