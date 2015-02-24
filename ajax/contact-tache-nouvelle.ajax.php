<?php
	// On ouvre la fiche contact
	$evenement = new Event($_POST['evenement']);
	
	// On ajoute le tag
	$tache[0] = $evenement->task_new($_POST['user'], $_POST['tache'], $_POST['deadline']);
	
	if (isset($tache[0]['user']))
	{
		// On récupère le nom de la fiche qui est concernée par cette tâche
		$nickname = User::get_login_by_ID($tache[0]['user']);
	}
	else
	{
		$nickname = 'Pas d\'utilisateur attribué';
	}
	
	// On ajoute cette donnée au tableau
	$tache[0]['user'] = $nickname;
	
	echo json_encode($tache);
?>