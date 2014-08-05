<?php

/*
 *	Fichier d'appel des scripts AJAX
 */

// On intègre les fonctions essentielles et l'appel aux classes LeQG
	require_once 'includes.php';


// On fait la liste des différents scripts pouvant être appelés ci-après
	$scripts = array('historique-ajout');

// On vérifie que le script demandé existe
	$script = $core->securisation_string($_GET['script']);
	
	if (!in_array($script, $scripts)) exit; // Si le script demandé n'existe pas, on arrête l'exécution de la page ici.
	
	
// On lance l'appel des différents scripts

	require_once('ajax/' . $script . '.ajax.php');

?>