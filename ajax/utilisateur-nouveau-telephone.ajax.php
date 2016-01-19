<?php
    $user = $_POST['user'];
    
    $valeur = $_POST['valeur'];
    
    // Si le champ correspond au téléphone ou au mobile, on supprime les espaces qui ne servent à rien
    $valeur = preg_replace('`[^0-9]`', '', $valeur);
    
    // On met à jour le contenu dans la base de données
    $query = 'UPDATE `users` SET `user_phone` = "' . $valeur . '" WHERE `user_id` = ' . $user;
    $noyau->query($query);

    return true;
?>