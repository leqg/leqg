<?php

	// On récupère les données du formulaire
	$nom = $_POST['nom'];
	$description = $_POST['description'];
	$contact = $_POST['fiche'];
	$interaction = $_POST['interaction'];
	
	// On créé d'abord le dossier et on récupère son ID
	$nouveau_dossier = $dossier->creation_rapide($contact, $nom, $description);
	
	// On effectue la liaison entre l'interaction et le dossier nouvelle créé
	$query = 'UPDATE historique SET dossier_id = ' . $nouveau_dossier . ' WHERE historique_id = ' . $interaction;
	$db->query($query);
	
	// On retourne sur la page de l'interaction
	$core->tpl_go_to('fiche', array('id' => $contact, 'interaction' => $interaction), true);
?>