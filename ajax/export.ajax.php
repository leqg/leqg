<?php

	// On récupère les informations
	$var = $_GET;
	
	// On retraite les critères complexes
	$var['criteres'] = trim($var['criteres'], ';');
	
	// On récupère la liste des contacts concernés
	$contacts = Contact::listing($var, 0, false);
	
	// On prépare le contenu du fichier sous forme de tableau
	$fichier = array();
	
	// On ouvre le fichier
	$nomFichier = 'export-' . User::ID() . '-' . date('Y-m-d-H\hi') . '-' . time() . '.csv';
	$file = fopen('exports/' . $nomFichier, 'w+');
	
	// On y entre la première ligne du fichier
	$entete = array(
		'nom',
		'nom_usage',
		'prenoms',
		'date_naissance',
		'adresse déclarée',
		'cp',
		'ville',
		'sexe',
		'email',
		'mobile',
		'fixe',
		'electeur',
		'bureau',
		'adresse électorale',
		'cp électoral',
		'ville électorale',
		'tags'
	);
	
	fputcsv($file, $entete, ';', '"');
	
	// Pour chaque contact, on recherche les informations et on ajoute une ligne
	foreach($contacts as $contact) {
		// On ouvre la fiche
		$c = new Contact(md5($contact));
		
		// On récupère les données
		unset($data);
		$data = $c->donnees();
		
		// On récupère les informations sur le bureau de vote
		$bureau = Carto::bureau($data['bureau_id']);
		
		// On récupère l'adresse
		if ($data['adresse_id']) {
			$adresse = Carto::detailAdresse($data['adresse_id']);
		} else {
			$adresse['immeuble_numero'] = '';
			$adresse['rue_nom'] = '';
			$adresse['code_postal'] = '';
			$adresse['commune_nom'] = '';
		}
		
		if ($data['immeuble_id']) {
			$immeuble = Carto::detailAdresse($data['immeuble_id']);
		} else {
			$immeuble['immeuble_numero'] = '';
			$immeuble['rue_nom'] = '';
			$immeuble['code_postal'] = '';
			$immeuble['commune_nom'] = '';
		}
		
		// On prépare la ligne à ajouter
		unset($ligne);
		$ligne = array(
			mb_convert_case($data['contact_nom'], MB_CASE_UPPER),
			mb_convert_case($data['contact_nom_usage'], MB_CASE_UPPER),
			mb_convert_case($data['contact_prenoms'], MB_CASE_TITLE),
			date('d/m/Y', strtotime($data['contact_naissance_date'])),
			$adresse['immeuble_numero'] . ' ' . mb_convert_case($adresse['rue_nom'], MB_CASE_TITLE),
			$adresse['code_postal'],
			mb_convert_case($adresse['commune_nom'], MB_CASE_UPPER),
			$data['contact_sexe'],
			$data['email'],
			$data['mobile'],
			$data['fixe'],
			($data['contact_electeur']) ? 'oui' : 'non',
			$bureau['bureau_numero'],
			$immeuble['immeuble_numero'] . ' ' . mb_convert_case($immeuble['rue_nom'], MB_CASE_TITLE),
			$immeuble['code_postal'],
			mb_convert_case($immeuble['commune_nom'], MB_CASE_UPPER),
			$data['contact_tags']
		);
		
		// On ajoute la ligne au fichier
		fputcsv($file, $ligne, ';', '"');
	}
	
	// On retraite le nom du fichier
	$f = 'exports/' . $nomFichier;
	
	// Une fois qu'il est connu, on télécharge le template de mail pour avertir l'utilisateur
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
	$query = $core->prepare('SELECT `email`, `firstname`, `lastname` FROM `compte` WHERE `id` = :id');
	$idCompte = User::ID();
	$query->bindParam(':id', $idCompte);
	$query->execute();
	$data = $query->fetch(PDO::FETCH_ASSOC);
	
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
	$mail->AddAddress($data['email'], $data['firstname'] . ' ' . $data['lastname']);
	$mail->Subject = $objet;
	$mail->MsgHTML($email);

	if ($mail->Send()) {
		echo 'email envoyé';
	} else {
		echo 'échec d\'envoi';
	}
	
?>