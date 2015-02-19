<?php
    if (is_string($_POST['objet']) && is_string($_POST['message']) && is_numeric($_POST['adresse']) && is_numeric($_POST['contact']))
    {
        	// On récupère le lien vers les BDD
        	$link = Configuration::read('db.link');
        	$zentrum = Configuration::read('db.core');
        	$user = User::ID();
        	$type = "email";
        	
        	// On lance l'envoi
        	$envoi = Campagne::envoi($_POST['objet'], $_POST['message'], $type);
        	
        	// On récupère l'adresse email
        	$query = $link->prepare('SELECT `coordonnee_email` FROM `coordonnees` WHERE `coordonnee_id` = :id');
        	$query->bindParam(':id', $_POST['adresse']);
        	$query->execute();
        	$adresse = $query->fetch(PDO::FETCH_ASSOC);
        	$adresse = $adresse['coordonnee_email'];
        	
        	// On lance le tracking
        	$query = $link->prepare('INSERT INTO `tracking` (`envoi_id`, `tracking_mail`) VALUES (:envoi, :mail)');
        	$query->bindValue(':envoi', $envoi);
        	$query->bindValue(':mail', $adresse);
        	$query->execute();
        	
        	// On ouvre la fiche du contact concerné
        	$contact = new contact(md5($_POST['contact']));
        	
        	// On charge le système de mail
        	$mail = Configuration::read('mail');
        	
        	// On prépare le message
        	$message = array(
            	'html' => nl2br($_POST['message']),
            'subject' => $_POST['objet'],
            'from_email' => 'noreply@leqg.info',
            'from_name' => 'LeQG',
            'to' => array(
                array(
                    'email' => $adresse,
                    'name' => $contact->noms(' ', ''),
                    'type' => 'to'
                )
            ),
            'headers' => array('Reply-To' => 'webmaster@leqg.info'),
            'track_opens' => true,
            'auto_text' => true
        	);
        	// mode asynchrone d'envoi du mail
        	$async = true;
        	
        	// on lance l'envoi du mail
        	$result = $mail->messages->send($message, $async);
        	
    	    // On met à jour le tracking avec les informations retournées
    	    $query = $link->prepare('UPDATE `tracking` SET `tracking_id` = :id, `tracking_status` = :status, `tracking_reject_reason` = :reject WHERE `envoi_id` = :envoi AND `tracking_mail` = :mail');
    	    $query->bindValue(':id', $result[0]['_id']);
    	    $query->bindValue(':status', $result[0]['status']);
    	    $query->bindValue(':reject', $result[0]['reject_reason']);
    	    $query->bindValue(':envoi', $envoi);
    	    $query->bindValue(':mail', $adresse);
    	    $query->execute();
    }
    else
    {
        return false;
    }
?>