<?php

	// On récupère les informations nécessaires
	$infos = array('fiche' => $_POST['fiche'],
			  	   'interaction' => $_POST['interaction'],
			  	   'type' => $_POST['type'],
			  	   'date' => $_POST['date'],
			  	   'lieu' => $_POST['lieu'],
			  	   'objet' => $_POST['objet'],
			  	   'notes' => $_POST['notes']);
	
	// On effectue les modifications dans la BDD
	$historique->modification($infos);
	
	// On retourne vers la page de départ
	$args_url = array('id' => $infos['fiche'], 'interaction' => $infos['interaction']);
	$core->tpl_go_to('fiche', $args_url, true);
?>