<?php

    // On récupère les informations
    $infos = $_POST;
    
    // On exécute la requête
    $query = $link->prepare('DELETE FROM `liaisons` WHERE (`ficheA` = :ficheA AND `ficheB` = :ficheB) OR (`ficheA` = :ficheB AND `ficheB` = :ficheA)');
    $query->bindParam(':ficheA', $infos['ficheA']);
    $query->bindParam(':ficheB', $infos['ficheB']);
    $query->execute();

?>