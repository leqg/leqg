<?php

	// On récupère les informations
	$texte = $_POST['texte'];
	$destinataire = strtr($_POST['destinataire'], ' ', '');

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
	
	print $result->id();
	print $result->uri();


?>