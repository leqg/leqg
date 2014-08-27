<?php

	// On recueille les informations pour les enregistrer dans la base de données
	$informations = $_POST;
	$titre = $core->securisation_string($_POST['titre']);
	$texte = $core->securisation_string($_POST['texte']);
	
	// On enregistre les informations dans la base
	$db->query('INSERT INTO	envois (compte_id, envoi_type, envoi_titre, envoi_texte )
				VALUES (' . $_COOKIE['leqg-user'] . ', "poste", "' . $titre . '", "' . $texte . '")');
				
	$entree = $db->insert_id;
	
	$core->tpl_go_to('poste', array('action' => 'nouveau', 'ciblage' => $entree), true); 

?>