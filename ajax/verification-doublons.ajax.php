<?php

/*
 *	Script de vérification de la présence de fiches similaires à celle proposé à l'ajout dans la base de données
 *	
 *	En cas de fiches similaires trouvées, cela propose :
 *	
 *		1. les fiches en question pour accéder à la fiche, en ajoutant les coordonnées entrées dans le formulaire précédent à la fiche choisie
 *		
 *		2. la possibilité de continuer le processus de création d'une nouvelle fiche par l'ajout d'une adresse
 *
 */


 	// On récupère les informations envoyées par le formulaire
	 	$infos = array(	'nom'		=> $_POST['nom'],
	 					'nom-usage'	=> $_POST['nom-usage'],
	 					'prenom'		=> $_POST['prenom'],
	 					'sexe'		=> $_POST['sexe'],
	 					'telephone'	=> $core->securisation_string($_POST['telephone']),
	 					'mobile'		=> $core->securisation_string($_POST['mobile']),
	 					'email'		=> $core->securisation_string($_POST['email']) );
 	
 	
 	// On va procéder à la recherche de fiches similaires
 		$contacts = $fiche->recherche($infos['prenom'], $infos['nom'], $infos['nom-usage'], $infos['sexe']);
 	
 		$core->debug($contacts);

?>