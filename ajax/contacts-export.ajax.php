<?php
	// On récupère la liste des tris
	if (isset($_GET['tri'])) :
	
		// On récupère la liste des tris
		$tri = $_GET['tri'];
		
		// On retraite la liste des tris sous forme d'un tableau d'arguments $args
		$tri = explode(',', $tri);
		$args = array();
		foreach($tri as $key => $t) :
			$t = explode(':', $t);
			$args[$t[0]] = $t[1];
		endforeach;
		
		// On lance la recherche des fiches correspondances aux arguments
		$f = $fiche->liste( 'fichier' , $args , true );
		
		// Une fois que le nom du fichier est connu, on télécharge le template de mail pour avertir l'utilisateur
		if ($f) :
			$email = file_get_contents('tpl/mail/export-reussi.tpl.html');
			$objet = 'LeQG – Votre export est prêt à être téléchargé.';
			
			// On insère dans le mail l'URL du fichier pour qu'il puisse être téléchargé
			$email = strtr($email, array('{URL}' => 'http://' . $config['SERVER']['url'] . '/' . $f));
		else :
			$email = file_get_contents('tpl/mail/export-echec.tpl.html');
			$objet = 'LeQG – Votre export a provoqué une erreur.';
		endif;
		
		// On recherche les informations concernant le compte connecté
		$query = 'SELECT * FROM `users` WHERE `user_id` = ' . $_COOKIE['leqg-user'];
		$sql = $noyau->query($query);
		$utilisateur = $core->formatage_donnees($sql->fetch_assoc());
		
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
		$mail->SetFrom('ne-pas-repondre@leqg.info', 'LeQG');
		$mail->AddReplyTo('tech@leqg.info', 'LeQG équipe technique');
		$mail->AddAddress($utilisateur['email'], $utilisateur['firstname'] . ' ' . $utilisateur['lastname']);
		$mail->Subject = $objet;
		$mail->MsgHTML($email);
	
		$mail->Send();
		
	else :
		
		return false;
	
	endif;
?>