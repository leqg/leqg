<?php
	// On récupère les informations
	$contact = $_GET['fiche'];
	$immeuble = $_GET['immeuble'];
	
	// On enregistre la nouvelle adresse
	$fiche->modificationAdresse($contact , $immeuble);
	
	$core->tpl_go_to('fiche', array('id' => $contact), true);
?>