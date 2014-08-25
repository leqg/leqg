<?php

	// On récupère les informations
	$formulaire = $_POST;
	
	// On réalise l'export et on récupère le nom du fichier
	$f = $fiche->export($formulaire);
	
	// Une fois qu'il est connu, on télécharge le template de mail pour avertir l'utilisateur
	if ($f) :
		$email = file_get_contents('tpl/mail/export-reussi.tpl.html');
		$objet = 'LeQG – Votre export est prêt à être téléchargé.';
		
		// On insère dans le mail l'URL du fichier pour qu'il puisse être téléchargé
		$email = strtr($email, array('{URL}' => $f));
	else :
		$email = file_get_contents('tpl/mail/export-echec.tpl.html');
		$objet = 'LeQG – Votre export a provoqué une erreur.';
	endif;
	
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
	$mail->SetFrom('noreply@leqg.info', 'LeQG');
	$mail->AddReplyTo('tech@leqg.info', 'LeQG équipe technique');
	$mail->AddAddress($user->get_the_email(), $user->get_the_nickname(););
	$mail->Subject = $objet;
	$mail->MsgHTML($email);

	$mail->Send();
	
?>