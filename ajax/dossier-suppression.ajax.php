<?php

	// On cherche les informations sur le dossier
	$d = $fiche->dossier($_GET['id']);
	
	// On supprimer ce dossier
	$query = 'DELETE FROM dossiers WHERE dossier_id = ' . $d['id'];
	$db->query($query);
	
	// On supprime toute occurence dans les interactions
	$query = 'UPDATE historique SET dossier_id = 0 WHERE dossier_id = ' . $d['id'];
	$db->query($query);
	
	// On supprime tous les fichiers rattachés à ce dossier
	$query = 'DELETE FROM fichiers WHERE dossier_id = ' . $d['id'];
	$db->query($query);
	
	// On supprime les tags relatifs au dossier supprimé
	$query = 'SELECT * FROM contacts WHERE contact_tags LIKE "%' . $d['nom'] . '%"';
	$sql = $db->query($query);
	
	while ($row = $sql->fetch_assoc()) {
		$tags = explode(',', $row['contact_tags']);
		$newtags = array();
		
		foreach ($tags as $tag) {
			if ($tag != $d['nom']) {
				$newtags[] = $tag;
			}
		}
		
		$nouveauxtags = implode(',', $newtags);
		$db->query('UPDATE contacts SET contact_tags = "' . $nouveauxtags . '" WHERE contact_id = ' . $row['contact_id']);
		unset($newtags);
		unset($nouveauxtags);
	}
	
	$core->tpl_go_to('dossiers', true);
?>