<?php
	// On ouvre la fiche contact
	$evenement = md5($_POST['evenement']);
	$evenement = new evenement($evenement);
	
	// On ajoute le tag
	$tache = $evenement->tache_ajout($_POST['user'], $_POST['tache']);
	
	if (isset($tache[0]['compte_id']))
	{
		// On récupère le nom de la fiche qui est concernée par cette tâche
		$nickname = User::get_login_by_ID($tache[0]['compte_id']);
	}
	else
	{
		$nickname = 'Pas d\'utilisateur attribué';
	}
	
	// On ajoute cette donnée au tableau
	$tache[0]['user'] = $nickname;
	
	echo json_encode($tache);
?>