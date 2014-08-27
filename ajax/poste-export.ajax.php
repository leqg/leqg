<?php
	
	if (isset($_GET['campagne'])) :
	
		// On récupère le numéro de la campagne à lancer
		$query = 'SELECT * FROM envois WHERE envoi_id = ' . $_GET['campagne'];
		$sql = $db->query($query);
		$campagne = $core->formatage_donnees($sql->fetch_assoc());
		
		// On récupère la liste des personnes à contacter
		$contacts = explode(',', $campagne['destinataire']);
		
		// On prépare leur numéros de téléphone dans un tableau numéros
		$lignes = array();
		foreach ($contacts as $c) {
			$query = 'SELECT * FROM contacts WHERE contact_id = ' . $c;
			$sql = $db->query($query); 
			$row = $sql->fetch_assoc();
			$row = $core->securisation_string($row); $core->debug($row);
			$query = 'SELECT		*
					  FROM		immeubles
					  LEFT JOIN	rues
					  ON			rues.rue_id = immeubles.rue_id
					  LEFT JOIN	communes
					  ON			communes.commune_id = rues.commune_id
					  LEFT JOIN	codes_postaux
					  ON			codes_postaux.commune_id = communes.commune_id
					  WHERE		immeubles.immeuble_id = ' . $row['immeuble_id'];
			$sql = $db->query($query);
			$carte = $sql->fetch_assoc();
			
			$lignes[] = array('nom' => $row['nom'],
							  'nom_usage' => $row['nom_usage'],
							  'prenoms' => $row['prenoms'],
							  'adresse' => $carte['immeuble_numero'] . ' ' . $carto['rue_nom'],
							  'cp' => $carte['code_postal'],
							  'ville' => $carte['commune_nom']);
				
			// On enregistre l'envoi dans l'historique du contact
			$infos = array( 'contact' => $c,
							'user' => $_COOKIE['leqg-user'],
							'type' => 'poste',
							'date' => date('d/m/Y'),
							'lieu' => 'leQG',
							'objet' => 'Campagne de publipostage',
							'texte' => '' );
			$historique->ajout( $infos['contact'] , $infos['user'] , $infos['type'] , $infos['date'] , $infos['lieu'] , $infos['objet'] , $infos['texte'] );
		}
	
		// On réalise l'export et on récupère le nom du fichier
		$nomFichier = 'export-' . $_COOKIE['leqg-user'] . '-' .date('Y-m-d-H\hi'). '-' . time() . '.csv';
		$f = fopen('exports/' . $nomFichier, 'w+');
		
		// On y entre la première ligne du fichier
		$entete = array(   'nom',
						   'nom_usage',
						   'prenoms',
						   'adresse',
						   'cp',
						   'ville');
		
		fputcsv($f, $entete, ';', '"');
		
		foreach ($lignes as $ligne) {
			$line = array($ligne['nom'], $ligne['nom_usage'], $ligne['prenoms'], $ligne['adresse'], $ligne['cp'], $ligne['ville']);
			
			fputcsv($f, $line, ';', '"');
		}
			
		// On ferme le fichier
		fclose($f);
		
		// On recherche les informations concernant le compte connecté
		$query = 'SELECT * FROM users WHERE user_id = ' . $_COOKIE['leqg-user'];
		$sql = $noyau->query($query);
		$utilisateur = $core->formatage_donnees($sql->fetch_assoc());
		
		// On envoit l'adresse du fichier par mail
			$email = file_get_contents('tpl/mail/publipostage-reussi.tpl.html');
			$sujet = 'LeQG – Votre fichier de publipostage est prêt à être téléchargé.';
			
			// On insère dans le mail l'URL du fichier pour qu'il puisse être téléchargé
			$email = strtr($email, array('{URL}' => 'http://' . $config['SERVER']['url'] . '/' . $f));

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
			$mail->AddAddress($utilisateur['email'], $utilisateur['firstname'] . ' ' . $utilisateur['lastname']);
			$mail->Subject = $sujet;
			$mail->MsgHTML(nl2br($texte));

			$mail->Send();
							
				
	endif;

?>