<?php
	
	if (isset($_GET['campagne'])) :
	
		// On récupère le numéro de la campagne à lancer
		$query = 'SELECT * FROM envois WHERE envoi_id = ' . $_GET['campagne'];
		$sql = $db->query($query);
		$campagne = $core->formatage_donnees($sql->fetch_assoc());
		
		// On récupère la liste des personnes à contacter
		$contacts = explode(',', $campagne['destinataire']);
		
		// On prépare leur numéros de téléphone dans un tableau numéros
		$numeros = array();
		foreach ($contacts as $contact) {
			$numero = $fiche->contact('mobile', false, true, $contact);
			$numeros[] = array($numero, $contact);
		}
	
		// On prépare le message
		$texte = $campagne['texte'];
		
		// On prépare le tableau des retours
		$reussites = array();
		$echecs = array();
		
		// On prépare le départ des SMS
		foreach ($numeros as $numero) {
			
			// On prépare l'envoi
			$message = new \Esendex\Model\DispatchMessage(
			    "LeQG", // Send from
			    $numero[0], // Send to any valid number
			    html_entity_decode($texte),
			    \Esendex\Model\Message::SmsType
			);
			
			// On assure le démarrage du service
			$service = new \Esendex\DispatchService($api['sms']['auth']);
			
			// On tente l'envoi du message
			$result = $service->send($message);
			
			if ($result) {
				$reussites[] = $numero[1];
			} else {
				$echecs[] = $numero[1];
			}
			
			// On enregistre l'envoi dans l'historique du contact
			$infos = array( 'contact' => $numero[1],
							'user' => $_COOKIE['leqg-user'],
							'type' => 'sms',
							'date' => date('d/m/Y'),
							'lieu' => 'leQG',
							'objet' => 'Envoi d\'un SMS',
							'texte' => $texte );
			$historique->ajout( $infos['contact'] , $infos['user'] , $infos['type'] , $infos['date'] , $infos['lieu'] , $infos['objet'] , $infos['texte'] );
					
			// On réinitialise pour l'envoi suivant	
			unset($message);
			unset($service);
			unset($result);
			unset($infos);
		}
		
		// On enregistre les réussites et les échecs
		$db->query('UPDATE envois SET envoi_statut = 2, envoi_reussites = "' . implode(',', $reussites) . '", envoi_echecs = "' . implode(',', $echecs) . '" WHERE envoi_id = ' . $campagne['id']);
				
	endif;

?>