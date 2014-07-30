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


// Système d'ajout d'un nouveau dossier
else if ($_GET['action'] == 'creation-dossier-etape1') {
	// On récupère et on retraite les données
	$titre = $core->securisation_string($_POST['titre']);
	$description = $core->securisation_string($_POST['description']);
	$fiches = $core->securisation_string($_POST['id']);
	
	// On ajoute ce nouveau dossier dans la base de données
	$fiche->dossier_ajout($titre, $description, $fiches, false);
}


// Système de recherche de fiches dans le cadre de la création d'un nouveau dossier ou d'une nouvelle tâche
else if ($_GET['action'] == 'recherche-fiche-creation') {
	// On récupère la liste des champs déjà entrés, pour les exclure de la recherche
	$fiches_exclues = $_POST['exclusion'];
	$fiches_exclues = explode(',', $fiches_exclues);
	
	$recherche_exclusion = " AND ";
	$nb_exclusions = count($fiches_exclues);
	$i = 1;
	foreach ($fiches_exclues as $f) {
		$recherche_exclusion .= "contact_id != " . $f;
		if ($i != $nb_exclusions) { $recherche_exclusion .= " AND "; }
		$i++;
	}
	
	// On récupère la recherche et on la reformate
	$recherche = $core->securisation_string($_POST['recherche']);
	$recherche = $fiche->recherche_fiche($recherche);
	
	$query = 'SELECT contact_id FROM contacts WHERE CONCAT_WS(" ", contact_prenoms, contact_nom, contact_nom_usage, contact_prenoms) LIKE "%' . $recherche . '%"' . $recherche_exclusion . ' LIMIT 0, 25';
	$sql = $db->query($query);
	
	while ($row = $sql->fetch_assoc()) {
		echo '<a href="' . $core->tpl_return_url('dossier', 'ajout', 'action', $_POST['id'] . '-' . $row['contact_id'], 'id') . '">';
			echo '<li class="ajout-fiche" data-fiche="' . $row['contact_id'] . '">';
				echo $fiche->nomByID($row['contact_id'], 'span class="nom"', true);
				echo '<span class="bouton-ajout">&#xe817;</span>';
			echo '</li>';
		echo '</a>';
	}
}
?>