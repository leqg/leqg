<?php
	if (isset($_GET)) {
		// On assure le démarrage du service
		$service = new \Esendex\DispatchService($api['sms']['auth']);

		// On récupère les informations
		$infos = array(
			'titre' => $_GET['titre'],
			'message' => $_GET['message']
		);
		
		// On va commencer par créer la campagne
		$idCampagne = Campagne::creation('sms', $infos);
		
		// On ouvre ensuite cette campagne
		$campagne = new Campagne(md5($idCampagne));
		
		// On récupère les données
		$var = $_GET;
		
		// On retraite les critères complexes
		$var['criteres'] = trim($var['criteres'], ';');

		// On charge les fiches correspondantes
		$contacts = Contact::listing($var, 0, false);
		
		// Pour chaque fiche, on créé un envoi
		foreach ($contacts as $contact) {
			// On ouvre la fiche contact pour récupérer le numéro de téléphone
			$c = new Contact(md5($contact)); unset($mobile);
			
			// On récupère le numéro de téléphone
			$mobile = preg_replace('#[^0-9]#', '', $c->get('mobile'));
					
			// On prépare l'envoi
			$message = new \Esendex\Model\DispatchMessage(
			    'LeQG', // Send from
			    $mobile, // Send to any valid number
			    $infos['message'],
			    \Esendex\Model\Message::SmsType
			);
			
			// On tente l'envoi du message
			$result = $service->send($message);
			
			// Si l'envoi a réussi
			if ($result) {
				// On récupère les informations sur l'utilisateur
				$u = User::ID();
				
				// On enregistre l'envoi pour commencer
				$query = $core->prepare('INSERT INTO `sms` (`user`, `destinataire`, `texte`) VALUES (:user, :destinataire, :message)');
				$query->bindParam(':user', $u);
				$query->bindParam(':destinataire', $mobile, PDO::PARAM_INT);
				$query->bindParam(':message', $infos['message']);
				$query->execute();
				
				// On récupère l'ID de l'enregistrement
				$envoi = $core->lastInsertId();
				
				// On rajoute l'élément dans l'historique
				$query = $link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`, `historique_objet`, `historique_suivi_id`, `historique_notes`, `campagne_id`) VALUES (:contact, :compte, "sms", NOW(), :objet, :suivi, :notes, :campagne)');
				$query->bindParam(':contact', $c->get('contact_id'), PDO::PARAM_INT);
				$query->bindParam(':compte', $u);
				$query->bindParam(':objet', $infos['titre']);
				$query->bindParam(':suivi', $envoi);
				$query->bindParam(':notes', $infos['message']);
				$query->bindParam(':campagne', $idCampagne, PDO::PARAM_INT);
				$query->execute();
			}
		}
	}
?>