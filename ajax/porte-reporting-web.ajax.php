<?php
    // On récupère les données
    if (isset($_POST['mission'], $_POST['contact'], $_POST['statut']) || isset($_GET['mission'], $_GET['contact'], $_GET['statut']))
    {
	    // On récupère les informations en question
	    $mission = (isset($_POST['mission'])) ? $_POST['mission'] : $_GET['mission'];
	    $contact = (isset($_POST['contact'])) ? $_POST['contact'] : $_GET['contact'];
	    $statut = (isset($_POST['statut'])) ? $_POST['statut'] : $_GET['statut'];
	    
	    // On ouvre la mission
	    $mission = new Mission($mission);
	    
	    // On effectue le reporting
	    $mission->reporting($contact, $statut);
		
		// On retourne un code 200
		http_response_code(200);
    }
    else {
	    // On retourne un code d'erreur
	    http_response_code(418);
    }
?>