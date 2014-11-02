<?php
	// On vérifie qu'un dossier a été envoyé
	if (isset($_POST['dossier'], $_POST['notes']))
	{
		// On récupère les données envoyées
		$dossier = $_POST['dossier'];
		$notes = $_POST['notes'];
		
		// On ouvre le dossier concerné
		$dossier = new Folder(md5($dossier));
		
		// On modifie la description
		$dossier->modifier('dossier_notes', $notes);
		
		return true;
	}
	else
	{
		return false;
	}
?>