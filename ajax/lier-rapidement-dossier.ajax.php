<?php

	if (is_numeric($_GET['interaction']) && is_numeric($_GET['dossier']) && is_numeric($_GET['fiche'])) {
		// On lie d'abord interaction et dossier
		$dossier->lierInteraction($_GET['interaction'] , $_GET['dossier']);
		
		// On ajoute la fiche au dossier si elle n'y était pas déjà
		$dossier->lierFiche($_GET['fiche'], $_GET['dossier']);
	}
	// On effectue la liaison entre l'interaction et le dossier nouvelle créé
	$query = 'UPDATE historique SET dossier_id = ' . $_GET['dossier'] . ' WHERE historique_id = ' . $_GET['interaction'];
	$db->query($query);
	
	// On rajoute le tag s'il n'existe pas déjà dans la colonne tag du contact
	$d = $fiche->dossier($_GET['dossier']);
	$query = 'UPDATE contacts SET contact_tag = contact_tag + ",' . $d['nom'] . '" WHERE contact_id = ' . $_GET['fiche'];
	$db->query($query);
	
	// On retourne sur la page de l'interaction
	$core->tpl_go_to('fiche', array('id' => $_GET['fiche'], 'interaction' => $_GET['interaction']), true);

?>