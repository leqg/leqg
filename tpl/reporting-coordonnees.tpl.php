<?php
	// On ouvre la mission
	$data = new Mission($_GET['mission']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::tpl_go_to('porte', true);

    // On récupère les données du formulaire
    $reporting = $_POST;
    
    // On transforme les données pour avoir un tableau electeur => [type,info]
    $coordonnees = array();
    foreach ($reporting as $report => $coordonnee) {
        // Si aucune coordonnée n'a été entré, on supprime l'information
        if (empty($coordonnee)) {
            unset($reporting[$report]);
        }
        
        // Sinon on retraite les informations
        else {
            // On récupère l'identifiant de l'électeur
            $electeur = explode('-', $report);
            
            // On retraite dans le tableau initial
            $reporting[$electeur[1]][$electeur[0]] = $coordonnee;
            
            // On supprime l'ancienne information
            unset($reporting[$report]);
        }
    }
    
    // Pour chaque contact, on enregistre l'information
    foreach ($reporting as $report => $infos) {
        $contact = new Contact(md5($report));
        
        // On enregistre l'adresse mail si elle existe
        if (isset($infos['email'])) {
            $contact->ajoutCoordonnees('email', $infos['email']);
        }
        
        // On regarde le type de numéro de téléphone puis on l'enregistre s'il existe
        if (isset($infos['phone'])) {
    		$numero = preg_replace('`[^0-9]`', '', $infos['phone']);
    		$premiersNums = $numero{0}.$numero{1};
    		
    		if ($premiersNums == 06 || $premiersNums == 07) {
                $contact->ajoutCoordonnees('mobile', $numero);
    		} else {
                $contact->ajoutCoordonnees('fixe', $numero);
    		}
        }
    }
    
    Core::tpl_go_to('reporting', array('mission' => $_GET['mission'], 'rue' => $_GET['rue']), true);
?>
