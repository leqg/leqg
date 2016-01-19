<?php
    // On vérifie qu'un code de mission a été entré
if ((isset($_GET['code']) || isset($_POST['code'])) && (isset($_GET['user']) || isset($_POST['user']))) {
    // On récupère le code de la mission
    $code = (isset($_GET['code'])) ? $_GET['code'] : $_POST['code'];
    $user = (isset($_GET['user'])) ? $_GET['user'] : $_POST['user'];
        
    // On ouvre la mission
    $mission = new Mission($code);
        
    // On invite la personne demandée
    if ($mission->invitation($user)) {
        Core::tpl_go_to('mission', array('code' => $code, 'admin' => 'invitations'), true);
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