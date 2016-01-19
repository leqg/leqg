<?php
    // On récupère les informations envoyées
if (isset($_POST['age'], $_POST['bureaux'], $_POST['thema'])) {
    // On fabrique un tableau d'arguments
    $args = array(
        'age' => $_POST['age'],
        'bureaux' => $_POST['bureaux'],
        'thema' => $_POST['thema']
    );
        
    // On récupère l'estimation
    $estimation = Rappel::estimation($args);
        
    // On retourne cette estimation
    echo $estimation;
}
?>