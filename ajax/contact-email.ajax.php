<?php
    if (is_string($_POST['objet']) && is_string($_POST['message']) && is_numeric($_POST['adresse']) && is_numeric($_POST['contact']))
    {
        	// On créé le lien vers la BDD Client
        	$dsn =  'mysql:host=' . Configuration::read('db.host') . 
        			';dbname=' . Configuration::read('db.basename');
        	$user = Configuration::read('db.user');
        	$pass = Configuration::read('db.pass');
        	$link = new PDO($dsn, $user, $pass);
        	
        	// On créé le lien vers la BDD Centrale
        	$dsn =  'mysql:host=' . Configuration::read('db.host') . 
        			';dbname=leqg';
        	$user = Configuration::read('db.user');
        	$pass = Configuration::read('db.pass');
        	$zentrum = new PDO($dsn, $user, $pass);
        	
        	// On récupère l'adresse email
        	$query = $link->prepare('SELECT `coordonnee_email` FROM `coordonnees` WHERE `coordonnee_id` = :id');
        	$query->bindParam(':id', $_POST['adresse']);
        	$query->execute();
        	$adresse = $query->fetch(PDO::FETCH_ASSOC);
        	$adresse = $adresse['coordonnee_email'];
        	
        	// On ouvre la fiche du contact concerné
        	$contact = new contact(md5($_POST['contact']));
        	
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
		$mail->AddAddress($adresse, $contact->noms(' ', ''));
		$mail->Subject = $_POST['objet'];
		$mail->MsgHTML(nl2br($_POST['message']));
        	
        	// On procède à l'envoi du mail
        	if ($mail->Send())
        	{
            // On ouvre ce nouvel événement
            $evenement = new evenement($_POST['contact'], false, true);
            
            // On enregistre les données
            $evenement->modification('historique_type', 'email');
            $evenement->modification('historique_date', date('d/m/Y'));
            $evenement->modification('historique_objet', $_POST['objet']);
            $evenement->modification('historique_notes', $_POST['message']);
            
            
            // On enregistre l'achat de ce SMS
            unset($query);
            $utilisateur = (isset($_COOKIE['leqg-user'])) ? $_COOKIE['leqg-user'] : 0;
            $query = $zentrum->prepare('INSERT INTO `email` (`user`, `destinataire`, `objet`, `texte`) VALUES (:user, :destinataire, :objet, :texte)');
            $query->bindParam(':user', $utilisateur);
            $query->bindParam(':destinataire', $adresse);
            $query->bindParam(':objet', $_POST['objet']);
            $query->bindParam(':texte', $_POST['message']);
            $query->execute();
            
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
?>