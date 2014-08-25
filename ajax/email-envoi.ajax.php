<?php

	// On récupère les informations
	$contact = $_POST['contact'];
	$texte = $_POST['message'];
	$objet = $_POST['objet'];
	$destinataire = $_POST['email'];

	// On prépare le mail demandé
	
		// On regarde quel fin de ligne est demandée
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $destinataire)) : $passage_ligne = '\r\n'; else : $passage_ligne = '\n'; endif;
		
		// Déclaration des messages au format texte et au format HTML
		$message_txt  = $texte;
		$message_html = $texte;
		
		// On prépare le boundary
		$boundary = '-----=' . md5(rand());
		
		// On défini le sujet
		$sujet = $objet;
		
		// On prépare les entêtes
		$header = 'From: "LeQG" <tech@leqg.info>' . $passage_ligne;
		$header.= 'Reply-to: "LeQG Équipe Client" <info@leqg.info>' . $passage_ligne;
		$header.= 'MIME-Version: 1.0' . $passage_ligne;
		$header.= 'Content-Type: multipart/alternative;' . $passage_ligne . ' boundary="'. $boundary . '"' . $passage_ligne;
		
		// On créé le message
		$message = $passage_ligne . '--' . $boundary . $passage_ligne;
		$message.= 'Content-Type: text/plain; charset="UTF-8"' . $passage_ligne;
		$message.= 'Content-Transfer-Encoding: 8bit' . $passage_ligne;
		$message.= $passage_ligne . $message_txt . $passage_ligne;
		
		$message.= $passage_ligne . '--' . $boundary . $passage_ligne;
		$message.= 'Content-Type: text/html; charset="UTF-8"' . $passage_ligne;
		$message.= 'Content-Transfer-Encoding: 8bit' . $passage_ligne;
		$message.= $passage_ligne . $message_html . $passage_ligne;
		
		$message.= $passage_ligne . '--' . $boundary . '--' . $passage_ligne;
		$message.= $passage_ligne . '--' . $boundary . '--' . $passage_ligne;
		
		// On envoi l'email
		mail($destinataire, $sujet, $message, $header);
		
		
	
	// On enregistre dans l'historique le SMS envoyé
	$infos = array( 'contact' => $contact,
					'user' => $_COOKIE['leqg-user'],
					'type' => 'email',
					'date' => date('d/m/Y'),
					'lieu' => 'leQG',
					'objet' => 'Envoi d\'un email',
					'texte' => $texte );
	$historique->ajout( $infos['contact'] , $infos['user'] , $infos['type'] , $infos['date'] , $infos['lieu'] , $infos['objet'] , $infos['texte'] );
	
	// On redirige vers la page de la fiche en question
	$core->tpl_go_to('fiche', array('id' => $contact), true);

?>