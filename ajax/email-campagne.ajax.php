<?php
	if (isset($_GET)) {
		// On récupère les informations
		$infos = array(
			'titre' => $_GET['titre'],
			'message' => $_GET['message']
		);
		
		// On va commencer par créer la campagne
		$idCampagne = Campagne::creation('email', $infos);
		
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
			
			// On récupère le nom des fiches
			$noms = $c->get('nom_affichage');
			
			// On récupère l'email
			$email = $c->get('email');
						
			// On démarre l'instance Mail
			$mail = new PHPMailer();
			
			// On contacte le serveur d'envoi SMTP
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->Host = $api['mail']['smtp']['host'];
			$mail->Port = $api['mail']['smtp']['port'];
			$mail->Username = $api['mail']['smtp']['user'];
			$mail->Password = $api['mail']['smtp']['pass'];
		
			// On configure le mail à envoyer
			$mail->CharSet = $api['mail']['charset'];
			$mail->SetFrom('noreply@leqg.info', 'LeQG.info');
			$mail->AddReplyTo('contact@leqg.info', 'LeQG.info');
			$mail->AddAddress($email, $noms);
			$mail->Subject = $infos['titre'];
			$mail->MsgHTML(nl2br($infos['message']));
			
			// Si l'envoi a réussi
			if ($mail->Send()) {
				// On récupère les informations sur l'utilisateur
				$u = User::ID();
				
				// On enregistre l'envoi pour commencer
				$query = $core->prepare('INSERT INTO `email` (`user`, `destinataire`, `objet`, `texte`) VALUES (:user, :destinataire, :objet, :message)');
				$query->bindParam(':user', $u);
				$query->bindParam(':destinataire', $email, PDO::PARAM_INT);
				$query->bindParam(':objet', $infos['titre']);
				$query->bindParam(':message', $infos['message']);
				$query->execute();
				
				// On récupère l'ID de l'enregistrement
				$envoi = $core->lastInsertId();
				
				// On rajoute l'élément dans l'historique
				$query = $link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`, `historique_objet`, `historique_suivi_id`, `historique_notes`, `campagne_id`) VALUES (:contact, :compte, "email", NOW(), :objet, :suivi, :notes, :campagne)');
				$contact_id = $c->get('contact_id');
				$query->bindParam(':contact', $contact_id, PDO::PARAM_INT);
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