<?php
    // On vérifie qu'un code de mission a été entré
    if ((isset($_GET['code']) || isset($_POST['code'])) && (isset($_GET['user']) || isset($_POST['user']))) {
        // On récupère le code de la mission
        $code = (isset($_GET['code'])) ? $_GET['code'] : $_POST['code'];
        $user = (isset($_GET['user'])) ? $_GET['user'] : $_POST['user'];
        
        // On ouvre la mission
        $mission = new Mission($code);
        
        // On change le statut de la mission comme ouvert et on redirige
        if ($mission->reponse(-1, $user)) {
            Core::tpl_go_to('porte', array('action' => 'missions'), true);
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