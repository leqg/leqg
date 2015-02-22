<?php
	// On vérifie qu'un dossier a été envoyé
	if (isset($_POST['dossier'], $_POST['titre']))
	{
		// On récupère les données envoyées
		$dossier = $_POST['dossier'];
		$titre = $_POST['titre'];
		
		// On ouvre le dossier concerné
		$dossier = new Dossier(md5($dossier));
		
		// On modifie la description
		$dossier->modifier('dossier_nom', $titre);
		
		return true;
	}
	else
	{
		return false;
	}
?>