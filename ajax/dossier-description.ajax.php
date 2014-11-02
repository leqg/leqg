<?php
	// On vérifie qu'un dossier a été envoyé
	if (isset($_POST['dossier'], $_POST['description']))
	{
		// On récupère les données envoyées
		$dossier = $_POST['dossier'];
		$description = $_POST['description'];
		
		// On ouvre le dossier concerné
		$dossier = new Folder(md5($dossier));
		
		// On modifie la description
		$dossier->modifier('dossier_description', $description);
		
		return true;
	}
	else
	{
		return false;
	}
?>