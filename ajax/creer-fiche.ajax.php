<?php

	if (empty($_POST['nom'])) $_POST['nom'] = '';
	if (empty($_POST['nomUsage'])) $_POST['nomUsage'] = '';
	if (empty($_POST['prenom'])) $_POST['prenom'] = '';

	// On récupère les informations contenues dans le $_POST
	$infos = array('nom' => $core->securisation_string($_POST['nom']),
				   'nom-usage' => $core->securisation_string($_POST['nomUsage']),
				   'prenoms' => $core->securisation_string($_POST['prenom']),
				   'sexe' => $_POST['sexe'],
				   'telephone' => $_POST['fixe'],
				   'mobile' => $_POST['mobile'],
				   'email' => $_POST['email'],
				   'date-naissance' => $_POST['dateNaissance'],
				   'immeuble' => 0,
				   'organisme' => '',
				   'fonction' => '');
	
	// On commence par créer la fiche
	$contact = $fiche->creerContact($infos);

	// On affiche le numéro ID pour la redirection AJAX
	echo $contact;
?>