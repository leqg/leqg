<?php

    // On récupère les informations
    $infos = $_POST;
    
    // On exécute la requête
    $query = $link->prepare('INSERT INTO `liaisons` (`ficheA`, `ficheB`) VALUES (:ficheA, :ficheB)');
    $query->bindParam(':ficheA', $infos['ficheA']);
    $query->bindParam(':ficheB', $infos['ficheB']);
    $query->execute();

?>