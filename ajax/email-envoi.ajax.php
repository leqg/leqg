<?php

	// On récupère les informations
	$contact = $_POST['contact'];
	$texte = $_POST['message'];
	$objet = $_POST['objet'];
	$destinataire = $_POST['email'];
	
	// On prépare le mail demandé
	
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
		$mail->SetFrom($api['mail']['from']['email'], $api['mail']['from']['nom']);
		$mail->AddReplyTo($api['mail']['reply']['email'], $api['mail']['reply']['nom']);
		$mail->AddAddress($destinataire, $fiche->nomByID($contact, '', true));
		$mail->Subject = $objet;
		$mail->MsgHTML(nl2br($texte));
		
	// On procède à l'envoi du mail
	if ( $mail->Send() ) {
		
		$infos = array( 'contact' => $contact,
						'user' => $_COOKIE['leqg-user'],
						'type' => 'courriel',
						'date' => date('d/m/Y'),
						'lieu' => 'leQG',
						'objet' => $objet,
						'texte' => $texte );
		$historique->ajout( $infos['contact'] , $infos['user'] , $infos['type'] , $infos['date'] , $infos['lieu'] , $infos['objet'] , $infos['texte'] );
	
		unset($mail);
		
		// On redirige vers la page de la fiche en question
		$core->tpl_go_to('fiche', array('id' => $contact, 'interaction' => $db->insert_id), true);
	
	} else {
		echo 'Contactez l\'administrateur système avec les informations suivantes :<br><br><pre>' . $mail->ErrorInfo . '</pre>';
	}

?>