<?php

require_once 'includes.php';


if ($_GET['action'] == 'maj-fiche') {
	
	// On met à jour le contenu de la fiche
	if (!empty($_POST['champ'])) {
		$champ = $_POST['champ'];
		
		if (!empty($_POST['valeur'])) {
			$valeur = $_POST['valeur'];
			
			if ($champ == 'mobile' || $champ == 'telephone') {
				// Si le champ correspond au téléphone ou au mobile, on supprime les espaces qui ne servent à rien
				$valeur = str_replace(' ', '', $valeur);
				$valeur = str_replace('_', '', $valeur);
			}
			
			// On met à jour le contenu dans la base de données
			$query = 'UPDATE contacts SET contact_' . $champ . ' = "' . $valeur . '" WHERE contact_id = ' . $_GET['id'];
			if (!empty($_GET['id'])) $db->query($query);
		} else {
			$query = 'UPDATE contacts SET contact_' . $champ . ' = NULL WHERE contact_id = ' . $_GET['id'];
			if (!empty($_GET['id'])) $db->query($query);
		}
	}
}

else if ($_GET['action'] == 'ajout-tag') {
	
	// On met à jour les tags de la fiche
	if (!empty($_POST['valeur']) && !empty($_GET['id'])) {
		$query = 'SELECT contact_tags FROM contacts WHERE contact_id = ' . $_GET['id'];
		$sql = $db->query($query);
		$row = $sql->fetch_array();
		
		// On ajoute le nouveau tag
		if (empty($row[0])) {
			$tags = array(addslashes(strtolower(utf8_decode($_POST['valeur']))));
		} else {
			// On récupère le tableau des tags
			$tags = explode(',', $row[0]);
			$tags[] = addslashes(strtolower(utf8_decode($_POST['valeur'])));
		}
		
		// On insère les tags dans la base de données
		$db->query('UPDATE contacts SET contact_tags = "' . implode(',', $tags) . '" WHERE contact_id = ' . $_GET['id']);
		
		// On insère aussi le tag dans la base de données tags pour index
		$db->query('INSERT INTO tags VALUES ("' . utf8_decode($_POST['valeur']) . '")');
	}
	
}

else if ($_GET['action'] == 'suppression-tag') {
	
	// On met à jour les tags de la fiche
	if (!empty($_POST['valeur']) && !empty($_GET['id'])) {
		$query = 'SELECT contact_tags FROM contacts WHERE contact_id = ' . $_GET['id'];
		$sql = $db->query($query);
		$row = $sql->fetch_array();
		
		// On récupère le tableau des tags
		$tags = explode(',', $row[0]);
		
		// On supprime le tag demandé
		if (in_array(utf8_decode($_POST['valeur']), $tags)) {
			$key = array_search(utf8_decode($_POST['valeur']), $tags);
			
			// On supprime cette clé
			unset($tags[$key]);
			
			// On enregistre le nouveau tableau des tags dans la base de données
			$query = 'UPDATE contacts SET contact_tags = "' . implode(',', $tags) . '" WHERE contact_id = ' . $_GET['id'];
			$db->query($query);
		}
	}
	
}

else if ($_GET['action'] == 'autocompletion-tag') {
	
	// On cherche les tags qui ressemble au tag entré
	$tag = utf8_decode($_POST['valeur']);
	
	$query = 'SELECT tag_nom FROM tags WHERE tag_nom LIKE "%' . $tag . '%"';
	$sql = $db->query($query);
	
	if ($sql->num_rows > 0) {
		while ($row = $sql->fetch_array()) {
			echo '<option value="' . utf8_encode($row[0]) . '">';
		}
	} else {
		return false;
	}
}

else if ($_GET['action'] == 'ajout-historique') {
	
	$date = strtotime(str_replace('/', '-', $_POST['date']));
	$objet = addslashes($_POST['objet']);
	$type = $_POST['type'];
	
	if(checkdate(date('m', $date), date('d', $date), date('Y', $date))) {
		$query = "	INSERT INTO historique (	contact_id,
												historique_type,
												historique_date,
												historique_objet)
					VALUES ('" . $_GET['id'] . "',
							'" . utf8_decode($type) . "',
							'" . date('Y-m-d', $date) . "',
							'" . $objet . "')";
		
		$db->query($query);
		
		$affichage = '<tr>';
			$affichage .= '<td>' . ucwords($type) . '</td>';
			$affichage .= '<td>' . date('d/m/Y', $date) . '</td>';
			$affichage .= '<td>' . stripslashes($objet) . '</td>';
		$affichage.= '</tr>';
		
		echo $affichage;
	}
}

else if ($_GET['action'] == 'retrait-tache') {
	if (!empty($_POST['fiche']) && !empty($_POST['tache'])) {
		$fiche = $_POST['fiche'];
		$tache = $_POST['tache'];
		
		// On recherche les informations sur la tâche
		$sql = $db->query('SELECT * FROM taches WHERE tache_id = ' . $tache);
		$row = $sql->fetch_assoc();
		
		// On retire de la liste des fiches associées à la tâche la fiche demandée
		$fiches = explode(',', trim($row['tache_contacts'], ','));
		$fiches_finales = array();
		
		foreach ($fiches as $id) {
			if ($id != $fiche) {
				$fiches_finales[] = $id;
			}
		}
		
		// On retraite le tableau des fiches associées à la fiche
		$fiches_finales = implode(',', $fiches_finales);
		
		// On met à jour la base de données
		$db->query('UPDATE taches SET tache_contacts = "' . $fiches_finales . '," WHERE tache_id = ' . $tache);
	}
}

else if ($_GET['action'] == 'dossiers-existants') {
	// On recherche les dossiers existants
	$dossiers = $fiche->dossier_recherche($_POST['nom']);
	
	// Si le nombre de dossier est supérieur ou égal à 1
	if (count($dossiers) >= 1) {
		// Il existe des dossiers, donc on affiche un texte d'introduction
		$html  =	'<h4>Des dossiers similaires existent déjà</h4>';
		$html .=	'<p>Certains dossiers existent déjà, voulez-vous simplement ajouter le contact à l\'un d\'eux ?</p>';
		
		// Puis on liste les dossiers, avec lien permettant d'ajouter le contact au dossier voulu
		$html .=	'<ul id="liste-dossiers-existants">';
		foreach ($dossiers as $dossier) {
			$html .= '<li>';
				$html .= '<a href="ajax-form.php?action=ajout-fiche-dossier&id=' . $dossier['id'] . '&fiche=' . $_POST['fiche'] . '">' . $dossier['nom'] . '</a>';
			$html .= '</li>';
		}
		$html .=	'</ul>';
		
		// On affiche le tout
		echo $html;
	} else {
		// On ne trouve rien, donc on n'affiche rien
		return false;
	}
}
?>