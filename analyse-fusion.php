<?php

	require_once 'includes.php';
	
	// On règle d'abord les paramètres
	if (isset($_GET['fichier'])) { $file = $_GET['fichier']; } else { exit; }
	$tag = array('militant', 'parti');


	// On lance la lecture du fichier
	$data = $csv->lectureFichier('csv/' . $file . '.csv');
	$lignes = count($data);
	
	
	// On prépare un tableau des anomalies
	$anomalies = array();
	$afficher = array();
	
	// On lance l'analyse ligne par ligne
	foreach ($data as $key => $line) :
	
		if ($key >= 0) :
		
			$donnees = array('nom'		=> trim($line[3]),
							 'prenom'	=> trim($line[4]),
							 'fixe'		=> trim($line[7]),
							 'mobile'	=> trim($line[9]),
							 'email'	=> trim($line[8]),
							 'adresse'	=> trim($line[10]),
							 'numero'	=> null,
							 'rue'		=> null,
							 'cp'		=> trim($line[11]),
							 'ville'	=> trim($line[12]));

			// On retraite les numéros de téléphone pour retirer tout ce qui n'est pas des chiffres
			$donnees['fixe'] = preg_replace('`[^0-9]`', '', $donnees['fixe']);
			$donnees['mobile'] = preg_replace('`[^0-9]`', '', $donnees['mobile']);
			
			// On recherche les informations sur la fiche concernée
			$query = 'SELECT * FROM `contacts` WHERE (`contact_nom` LIKE "%' . $core->formatage_recherche($donnees['nom']) . '%" OR `contact_nom_usage` LIKE "%' . $core->formatage_recherche($donnees['nom']) . '%") AND `contact_prenoms` LIKE "%' . $core->formatage_recherche($donnees['prenom']) . '%" AND `contact_tags` LIKE "%' . implode(',', $tag) . '%"';
			$sql = $db->query($query);
			
			if ($sql->num_rows == 1) :
				
				// On récupère les informations de la fiche
				$row = $sql->fetch_assoc();
				
				// On regarde si des informations diffères et on créé un rapport
				if ($row['contact_mobile'] != $donnees['mobile']) $anomalies[] = array($row['contact_id'], 'mobile', trim($line[9]));
				if ($row['contact_telephone'] != $donnees['fixe']) $anomalies[] = array($row['contact_id'], 'mobile', trim($line[7]));
				if ($row['contact_email'] != $donnees['email']) $anomalies[] = array($row['contact_id'], 'mobile', trim($line[8]));
				
			endif;
		
		else :
		
			break;
			
		endif;
	
	endforeach;
	
	// On prend toutes les anomalies, et on les rentre dans la base
	foreach ($anomalies as $anomalie) :
	
		$query = 'INSERT INTO `fusion_erreurs` (`contact_id`, `fusion_erreur_case`, `fusion_erreur_entree`) VALUES (' . $anomalie[0] . ', "' . $anomalie[1] . '", "' . $anomalie[2] . '")';
		$db->query($query);
	
	endforeach;
	
	$core->debug($anomalies);
?>