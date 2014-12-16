<?php
    if (isset($_POST['mission']) || isset($_GET['mission'])) {
	    // On récupère l'identifiant de la mission
	    $mission = (isset($_POST['mission'])) ? $_POST['mission'] : $_GET['mission'];
	    
	    // On ouvre la mission
	    $mission = new Mission($mission);
	    
	    // On lance la fermeture de la mission
	    $mission->cloture();
	    
	    // On retourne le code de validation
	    http_response_code(200);
    }
    else {
	    // On retourne une erreur
	    http_response_code(418);
    }
?>