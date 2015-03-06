<?php
    if (is_string($_POST['objet']) && is_string($_POST['message']) && is_numeric($_POST['adresse']) && is_numeric($_POST['contact']))
    {
        	// On récupère le lien vers les BDD
        	$link = Configuration::read('db.link');
        	$zentrum = Configuration::read('db.core');
        	$user = User::ID();
        	$type = "email";
        	
        	// On récupère l'adresse email
        	$query = $link->prepare('SELECT `coordonnee_email` FROM `coordonnees` WHERE `coordonnee_id` = :id');
        	$query->bindParam(':id', $_POST['adresse']);
        	$query->execute();
        	$adresse = $query->fetch(PDO::FETCH_ASSOC);
        	$adresse = $adresse['coordonnee_email'];
        	
        	// On ouvre la fiche du contact concerné
        	$contact = new People($_POST['contact']);
        	
        	// On charge le système de mail
        	$mail = Configuration::read('mail');
        	
        	// On prépare le message
        	$message = array(
            	'html' => nl2br($_POST['message']),
            'subject' => $_POST['objet'],
            'from_email' => Configuration::read('mail.sender.mail'),
            'from_name' => Configuration::read('mail.sender.name'),
            'to' => array(
                array(
                    'email' => $adresse,
                    'name' => $contact->get('nom_complet'),
                    'type' => 'to'
                )
            ),
            'headers' => array('Reply-To' => Configuration::read('mail.replyto')),
            'track_opens' => true,
            'auto_text' => true,
            'subaccount' => Configuration::read('client')
        	);
        	// mode asynchrone d'envoi du mail
        	$async = true;
        	
        	// on lance l'envoi du mail
        	$result = $mail->messages->send($message, $async);
        	
    	    // On met à jour le tracking avec les informations retournées
    	    $campaign = 0;
        $query = Core::query('campaign-tracking');
        $query->bindValue(':campaign', $campaign, PDO::PARAM_INT);
        $query->bindValue(':id', $result[0]['_id']);
        $query->bindValue(':email', $result[0]['email']);
        $query->bindValue(':status', $result[0]['status']);
        $query->execute();
        
        $event = Event::create($contact->get('id'));
        $event = new Event($event);
        $event->update('historique_type', 'courriel');
        $event->update('historique_objet', $_POST['objet']);
        $event->update('historique_notes', $_GET['message']);
    }
    else
    {
        return false;
    }
?>