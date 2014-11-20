<?php
	if (isset($_GET)) {
		// On récupère les informations
		$infos = array(
			'titre' => $_GET['titre'],
			'message' => $_GET['message']
		);
		
		// On va commencer par créer la campagne
		$idCampagne = Campagne::creation('publi', $infos);
		
		// On ouvre ensuite cette campagne
		$campagne = new Campagne(md5($idCampagne));
		
		// On récupère les données
		$var = $_GET;
		
		// On retraite les critères complexes
		$var['criteres'] = trim($var['criteres'], ';');

		// On charge les fiches correspondantes
		$contacts = Contact::listing($var, 0, false);
	
		// On prépare le contenu du fichier sous forme de tableau
		$fichier = array();
		
		// On ouvre le fichier
		$nomFichier = 'publi-' . md5($idCampagne) . '.csv';
		$file = fopen('exports/' . $nomFichier, 'w+');
		
		// On y entre la première ligne du fichier
		$entete = array(
			'nom',
			'nom_usage',
			'prenoms',
			'numero',
			'adresse',
			'cp',
			'ville',
			'origine'
		);
	
		fputcsv($file, $entete, ';', '"');
		
		// Pour chaque fiche, on créé un envoi
		foreach ($contacts as $idContact) {
			// On ouvre la fiche contact pour récupérer le numéro de téléphone
			$contact = new Contact(md5($idContact));
			
			// On récupère les informations sur l'adresse déclarée
			if ($contact->get('adresse_id')) {
				$adresse = Carto::detailAdresse($contact->get('adresse_id'));
				$adresse['origine'] = 'fichier';
			}
			// S'il n'y en a pas, on récupère les informations sur l'adresse électorale
			else if ($contact->get('immeuble_id')) {
				$adresse = Carto::detailAdresse($contact->get('immeuble_id'));
				$adresse['origine'] = 'liste';
			}
			// S'il n'y en a pas, on vide les champs
			else {
				$adresse['immeuble_numero'] = '';
				$adresse['rue_nom'] = '';
				$adresse['code_postal'] = '';
				$adresse['commune_nom'] = '';
				$adresse['origine'] = '';
			}
			
			// On formate les informations du contact dans un tableau
			unset($datas);
			$datas = array(
				'nom' => mb_convert_case($contact->get('contact_nom'), MB_CASE_UPPER),
				'nom_usage' => mb_convert_case($contact->get('contact_nom_usage'), MB_CASE_UPPER),
				'prenoms' => mb_convert_case($contact->get('contact_prenoms'), MB_CASE_TITLE),
				'numero' => $adresse['immeuble_numero'],
				'adresse' => mb_convert_case(trim($adresse['rue_nom']), MB_CASE_TITLE),
				'cp' => $adresse['code_postal'],
				'ville' => mb_convert_case($adresse['commune_nom'], MB_CASE_UPPER),
				'origine' => $adresse['origine']
			);

			// Si l'ajout au fichier a réussi
			if (fputcsv($file, $datas, ';', '"')) {
				// On récupère les informations sur l'utilisateur
				$user = User::ID();
				
				// On rajoute l'élément dans l'historique
				$query = $link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`, `historique_objet`, `historique_suivi_id`, `historique_notes`, `campagne_id`) VALUES (:contact, :compte, "publi", NOW(), :objet, :suivi, :notes, :campagne)');
				$contact_id = $contact->get('contact_id');
				$suivi_id = 0;
				$query->bindParam(':contact', $contact_id, PDO::PARAM_INT);
				$query->bindParam(':compte', $user, PDO::PARAM_INT);
				$query->bindParam(':objet', $infos['titre']);
				$query->bindParam(':suivi', $suivi_id, PDO::PARAM_INT);
				$query->bindParam(':notes', $infos['message']);
				$query->bindParam(':campagne', $idCampagne, PDO::PARAM_INT);
				$query->execute();
			}
		}
	}
?>