<?php
	
	if (isset($_GET['campagne'])) :
	
		// On récupère les informations de réglage
		$query = 'SELECT * FROM reglages WHERE nom = "email-expediteur"';
		$sql = $db->query($query);
		$expediteur = $sql->fetch_assoc();
		$expediteur = $expediteur['valeur'];

		$query = 'SELECT * FROM reglages WHERE nom = "email-expediteur-adresse"';
		$sql = $db->query($query);
		$expediteur_email = $sql->fetch_assoc();
		$expediteur_email = $expediteur_email['valeur'];

		// On récupère le numéro de la campagne à lancer
		$query = 'SELECT * FROM envois WHERE envoi_id = ' . $_GET['campagne'];
		$sql = $db->query($query);
		$campagne = $core->formatage_donnees($sql->fetch_assoc());
		
		// On récupère la liste des personnes à contacter
		$contacts = explode(',', $campagne['destinataire']);
		
		// On prépare leur numéros de téléphone dans un tableau numéros
		$emails = array();
		foreach ($contacts as $contact) {
			$email = $fiche->contact('email', false, true, $contact);
			$emails[] = array($email, $contact);
		}
	
		// On prépare le message
		$sujet = html_entity_decode($campagne['titre']);
		$texte = html_entity_decode($campagne['texte']);
		
		// On prépare le tableau des retours
		$reussites = array();
		$echecs = array();
		
		// On prépare le départ des SMS
		foreach ($emails as $email) {
			
			// On démarre l'instance
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
			$mail->SetFrom($expediteur_email, $expediteur);
			$mail->AddReplyTo($expediteur_email, $expediteur);
			$mail->AddAddress($email[0], $fiche->nomByID($contact, '', true));
			$mail->Subject = $sujet;
			$mail->MsgHTML(nl2br($texte));

			
			if ($mail->Send()) {
				$reussites[] = $email[1];
				
				// On enregistre l'envoi dans l'historique du contact
				$infos = array( 'contact' => $email[1],
								'user' => $_COOKIE['leqg-user'],
								'type' => 'courriel',
								'date' => date('d/m/Y'),
								'lieu' => 'leQG',
								'objet' => $campagne['titre'],
								'texte' => $campagne['texte'] );
				$historique->ajout( $infos['contact'] , $infos['user'] , $infos['type'] , $infos['date'] , $infos['lieu'] , $infos['objet'] , $infos['texte'] );
			} else {
				$echecs[] = $email[1];
			}
					
			// On réinitialise pour l'envoi suivant	
			unset($mail);
			unset($result);
			unset($infos);
		}
		
		// On enregistre les réussites et les échecs
		$db->query('UPDATE envois SET envoi_statut = 2, envoi_reussites = "' . implode(',', $reussites) . '", envoi_echecs = "' . implode(',', $echecs) . '" WHERE envoi_id = ' . $campagne['id']);
				
	endif;

?>