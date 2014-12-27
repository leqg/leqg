<?php
    // On vérifie qu'un code de mission a été entré
    if (isset($_GET['code']) || isset($_POST['code'])) {
        // On récupère le code de la mission
        $code = (isset($_GET['code'])) ? $_GET['code'] : $_POST['code'];
        
        // On ouvre la mission
        $mission = new Mission($code);
        
        // On change le statut de la mission comme ouvert et on redirige
        if ($mission->ouvrir()) {
            Core::tpl_go_to('mission', array('code' => $code), true);
        }
        
        // En cas d'erreur, on affiche un code d'erreur
        else {
            http_response_code(418);
        }
    }
    
    // Sinon, on retourne un code d'erreur
    else {
        http_response_code(418);
    }
?>