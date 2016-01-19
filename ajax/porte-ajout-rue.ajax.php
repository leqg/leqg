<?php

    // On récupère les informations
if (isset($_POST['rue'], $_POST['mission']) || isset($_GET['rue'], $_GET['mission'])) {
    // On récupère les données
    $rue = (isset($_POST['rue'])) ? $_POST['rue'] : $_GET['rue'];
    $mission = (isset($_POST['mission'])) ? $_POST['mission'] : $_GET['mission'];
        
    // On ouvre la mission
    $mission = new Mission($mission);
        
    // On ajoute la rue
    $mission->ajoutRue($rue);
        
    // On retourne un code de réussite
    http_response_code(200);
}
    
else {
    // On retourne un code d'erreur
    http_response_code(418);
}
    
?>