<?php

	require_once 'includes.php';
	
	// On règle d'abord les paramètres
	$file = 'parti';
	$tag = 'militant';


	// On lance la lecture du fichier
	$data = $csv->lectureFichier('csv/' . $file . '.csv');
	$lignes = count($data);
	
	
	// On prépare un tableau des anomalies
	$anomalies = array();
	
	// On lance l'analyse ligne par ligne
	foreach ($data as $key => $line) :
	
		// On ne lance l'analyse que s'il ne s'agit pas de la première ligne d'entête
		if ($key > 0) :
		
			$donnees = array('nom'		=> $line[3],
							 'prenom'	=> $line[4],
							 'fixe'		=> $line[7],
							 'mobile'	=> $line[9],
							 'email'	=> $line[8],
							 'adresse'	=> $line[10],
							 'numero'	=> null,
							 'rue'		=> null,
							 'cp'		=> $line[11],
							 'ville'	=> $line[12]);
						
							 
			// On commence par retraiter l'adresse
				$rue = explode(' ', $line[10], 2);
				$donnees['numero'] = $rue[0];
				$donnees['rue'] = $rue[1];
			
			
			// On retraite les numéros de téléphone pour retirer tout ce qui n'est pas des chiffres
				$donnees['fixe'] = preg_replace('`[^0-9]`', '', $donnees['fixe']);
				$donnees['mobile'] = preg_replace('`[^0-9]`', '', $donnees['mobile']);
			
			
			// On commence d'abord par récupérer l'ID de la ville en question
				$query = 'SELECT * FROM communes WHERE commune_nom LIKE "' . $core->formatage_recherche($donnees['ville']) . '" LIMIT 0,1';
				$sql = $db->query($query); $row = $sql->fetch_assoc();
				$code['ville'] = $row['commune_id'];

				
			// On continu en vérifiant si la rue existe déjà
				$query = 'SELECT * FROM rues WHERE commune_id = ' . $code['ville'] . ' AND rue_nom LIKE "%' . $core->formatage_recherche($donnees['rue']) . '%" LIMIT 0,1';
				$sql = $db->query($query); $nb = $sql->num_rows;
			
				// S'il existe déjà une rue dans la base de données, on récupère l'identifiant
				if ($nb == 1) {
					$row = $sql->fetch_assoc();
					$code['rue'] = $row['rue_id'];
				} else {
					// On rajoute la rue dans la base de données
					$query = 'INSERT INTO rues (commune_id, rue_nom) VALUES (' . $code['ville'] . ', "' . $donnees['rue'] . '")';
	//				$db->query($query);
					$code['rue'] = $db->insert_id;
				}
		
		
			// On regarde ensuite si l'immeuble existe déjà dans cette rue
				$query = 'SELECT * FROM immeubles WHERE rue_id = ' . $code['rue'] . ' AND immeuble_numero LIKE "' . $donnees['numero'] . '" LIMIT 0,1';
				$sql = $db->query($query); $nb = $sql->num_rows;
				
				// S'il existe déjà un immeuble dans la base de données, on récupère l'identifiant
				if ($nb == 1) {
					$row = $sql->fetch_assoc();
					$code['immeuble'] = $row['immeuble_id'];
				} else {
					// On rajoute l'immeuble dans la base de données
					$query = 'INSERT INTO immeubles (bureau_id, rue_id, immeuble_numero) VALUES (' . $code['bureau'] . ', ' . $code['rue'] . ', "' . $donnees['immeuble'] . '")';
	//				$db->query($query);
					$code['immeuble'] = $db->insert_id;
				}
				
			
			// On recherche maintenant une fiche similaire
				$query = 'SELECT contact_id, immeuble_id FROM contacts WHERE ( contact_nom LIKE "%' . $core->formatage_recherche($donnees['nom']) . '%" OR contact_nom_usage LIKE "' . $core->formatage_recherche($donnees['nom']) . '" ) AND contact_prenoms LIKE "' . $core->formatage_recherche($donnees['prenom']) . '%" ORDER BY contact_nom, contact_nom_usage, contact_prenoms ASC';
				$sql = $db->query($query);
				
				if ($sql->num_rows >= 1) :
				
					if ($sql->num_rows == 1) :
					
						$row = $sql->fetch_assoc();
						$code['fiche'] = $row['contact_id'];
						
					else :
					
						// On prépare un tableau pour voir quels sont les éléments qui répondent à la même discrimination
						$discri = array();
					
						// On fait la boucle pour voir s'il existe des critères de discrimination communs
						while ($row = $sql->fetch_assoc()) : if ($row['immeuble_id'] == $code['immeuble']) $discri[] = $row['contact_id']; endwhile;
						
						// On regarde s'il existe des fiches qui résistent à la discrimination
						if (count($discri) > 0) :
							
							// S'il existe une fiche qui résiste, on récupère l'ID
							if (count($discri) == 1) :
							
								$code['fiche'] = $row['contact_id'];
							
							// S'il existe plusieurs fiches, on note l'anomalie
							else :
								
								$code['fiche'] = 0;
								$anomalies[] = array('multi-adresse', $donnees, $code);
															
							endif;
						
						// Si aucune fiche ne correspond
						else :
						
							$code['fiche'] = 0;
							$anomalies[] = array('multi-nom', $donnees, $code);
						
						endif;
						
						// On vide la boucle des discriminés
						unset($discri);
					
					endif;
					
				else :
				
					// Aucune fiche
					$code['fiche'] = null;
					//$anomalies[] = array('aucun', $donnees, $code);
				
				endif;
		
		elseif ($key != 0) :
		
			break;
		
		endif;
	
	endforeach;
	
	$core->debug($anomalies);


?>