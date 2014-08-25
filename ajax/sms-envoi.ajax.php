<?php

	// On récupère les informations
	$contact = $_POST['contact'];
	$texte = $_POST['message'];
	$destinataire = $_POST['numero'];

	// On prépare l'envoi
	$message = new \Esendex\Model\DispatchMessage(
	    "Webapp", // Send from
	    $destinataire, // Send to any valid number
	    $texte,
	    \Esendex\Model\Message::SmsType
	);
	
	// On assure le démarrage du service
	$service = new \Esendex\DispatchService($api['sms']['auth']);
	
	// On tente l'envoi du message
	$result = $service->send($message);
	
	// On enregistre dans l'historique le SMS envoyé
	$infos = array( 'contact' => $contact,
					'user' => $_COOKIE['leqg-user'],
					'type' => 'sms',
					'date' => date('d/m/Y'),
					'lieu' => 'leQG',
					'objet' => 'Envoi d\'un SMS',
					'texte' => $texte );
	$historique->ajout( $infos['contact'] , $infos['user'] , $infos['type'] , $infos['date'] , $infos['lieu'] , $infos['objet'] , $infos['texte'] );
	
	// On redirige vers la page de la fiche en question
	$core->tpl_go_to('fiche', array('id' => $contact), true);

?>