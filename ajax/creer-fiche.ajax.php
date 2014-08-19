<?php

	// On récupère les informations contenues dans le $_POST
	$infos = array('nom' => $core->securisation_string($_POST['nom']),
				   'nom-usage' => $core->securisation_string($_POST['nomUsage']),
				   'prenoms' => $core->securisation_string($_POST['prenom']),
				   'sexe' => $_POST['sexe'],
				   'telephone' => $_POST['fixe'],
				   'mobile' => $_POST['mobile'],
				   'email' => $_POST['email'],
				   'date-naissance' => $_POST['dateNaissance'],
				   'immeuble' => 0);
	
	// On commence par créer la fiche
	$contact = $fiche->creerContact($infos);

	// On affiche le numéro ID pour la redirection AJAX
	echo $contact;
?>