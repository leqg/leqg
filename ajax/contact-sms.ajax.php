<?php
if (is_string($_POST['message']) && is_numeric($_POST['numero']) && is_numeric($_POST['contact'])) {
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
            
     // On règle l'expéditeur
     $expediteur = 'LeQG';
            
     // On récupère le numéro de téléphone
     $query = $link->prepare('SELECT `coordonnee_numero` FROM `coordonnees` WHERE `coordonnee_id` = :id');
     $query->bindParam(':id', $_POST['numero']);
     $query->execute();
     $numero = $query->fetch(PDO::FETCH_ASSOC);
     $numero = $numero['coordonnee_numero'];
            
     // On prépare l'envoi
    $message = new \Esendex\Model\DispatchMessage(
        $expediteur, // Send from
        $numero, // Send to any valid number
        $_POST['message'],
        \Esendex\Model\Message::SmsType
    );
            
     // On assure le démarrage du service
     $service = new \Esendex\DispatchService($api['sms']['auth']);
            
     // On tente l'envoi du message
     $result = $service->send($message);
    
    // Si le message est envoyé, on l'entre dans l'historique, sinon c'est une erreur
    if ($result) {
        // On ouvre ce nouvel événement
        $evenement = new evenement($_POST['contact'], false, true);
            
        // On enregistre les données
        $evenement->modification('historique_type', 'sms');
        $evenement->modification('historique_date', date('d/m/Y'));
        $evenement->modification('historique_objet', $_POST['message']);
            
        // On enregistre l'achat de ce SMS
        unset($query);
        $utilisateur = (isset($_COOKIE['leqg-user'])) ? $_COOKIE['leqg-user'] : 0;
        $query = $zentrum->prepare('INSERT INTO `sms` (`user`, `destinataire`, `texte`) VALUES (:user, :destinataire, :texte)');
        $query->bindParam(':user', $utilisateur);
        $query->bindParam(':destinataire', $numero);
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