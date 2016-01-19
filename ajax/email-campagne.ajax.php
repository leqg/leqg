<?php
if (isset($_GET)) {
    // On récupère les informations
    $infos = array(
    'titre' => $_GET['titre'],
    'message' => $_GET['message']
    );
        
    // On va commencer par créer la campagne
    $idCampagne = Campagne::creation('email', $infos);
        
    // On ouvre ensuite cette campagne
    $campagne = new Campagne(md5($idCampagne));
        
    // On récupère les données
    $var = $_GET;
        
    // On retraite les critères complexes
    $var['criteres'] = trim($var['criteres'], ';');

    // On charge les fiches correspondantes
    $contacts = Contact::listing($var, 0, false);
    $listing = array();
        
    // Pour chaque fiche, on créé un envoi
    foreach ($contacts as $contact) {
        // On ouvre la fiche contact pour récupérer le numéro de téléphone
        $c = new Contact(md5($contact)); unset($mobile);
                        
        // On démarre l'instance Mail
        $listing[] = array(
                    'email' => $c->get('email'),
                    'name' => $c->get('nom_affichage'),
                    'type' => 'to'
        );
    }
        
        // On charge le système de mail
        $mail = Configuration::read('mail');
            
        // On prépare le message
        $message = array(
            'html' => nl2br($infos['message']),
           'subject' => $infos['titre'],
           'from_email' => 'noreply@leqg.info',
           'from_name' => 'LeQG',
           'to' => $listing,
           'headers' => array('Reply-To' => 'webmaster@leqg.info'),
           'track_opens' => true,
           'auto_text' => true
        );
        // mode asynchrone d'envoi du mail
        $async = true;
            
        // on lance l'envoi du mail
        $result = $mail->messages->send($message, $async);
            
        Core::debug($result, false);

}
?>