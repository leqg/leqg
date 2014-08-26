<?php

	// On recueille les informations pour les enregistrer dans la base de données
	$informations = $_POST;
	$titre = $core->securisation_string($_POST['objet']);
	$texte = $core->securisation_string($_POST['texte']);
	
	// On enregistre les informations dans la base
	$db->query('INSERT INTO	envois (compte_id, envoi_type, envoi_titre, envoi_texte )
				VALUES (' . $_COOKIE['leqg-user'] . ', "email", "' . $titre . '", "' . $texte . '")');
				
	$entree = $db->insert_id;
	
	$core->tpl_go_to('email', array('action' => 'nouveau', 'ciblage' => $entree), true); 

?>