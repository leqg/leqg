<?php

	// On récupère les informations contenues dans le $_POST
	$infos = array('nom' => $core->securisation_string($_POST['nom']),
				   'nom-usage' => $core->securisation_string($_POST['nomUsage']),
				   'prenoms' => $core->securisation_string($_POST['prenom']),
				   'sexe' => $_POST['sexe'],
				   'telephone' => $_POST['fixe'],
				   'mobile' => $_POST['mobile'],
				   'email' => $_POST['email'],
				   'immeuble' => $_POST['immeuble']);
	
	// On formate correctement certaines données
	$infos['mobile'] = preg_replace('`[^0-9]`', '', $infos['mobile']);
	$infos['telephone'] = preg_replace('`[^0-9]`', '', $infos['telephone']);
	
	if ($infos['telephone'] == '0000000000') $infos['telephone'] = null;
	if ($infos['mobile'] == '0000000000') $infos['mobile'] = null;
	if ($infos['email'] == '') $infos['email'] = null;
	
	// On commence par créer la fiche
	$contact = $fiche->creerContact($infos);

	// On affiche le numéro ID pour la redirection AJAX
	echo $contact;
?>