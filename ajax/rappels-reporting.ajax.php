<?php
    // On sauvegarde les données envoyées
if (isset($_GET['notes'], $_GET['contact'], $_GET['argumentaire'])) {
        
    // On se connecte à la BDD
    $link = Configuration::read('db.link');
        
    // On exécute la requête de sauvegarde
    $query = $link->prepare('UPDATE `rappels` SET `rappel_reporting` = :notes WHERE `contact_id` = :contact AND `argumentaire_id` = :argumentaire');
    $query->bindParam(':notes', $_GET['notes']);
    $query->bindParam(':contact', $_GET['contact'], PDO::PARAM_INT);
    $query->bindParam(':argumentaire', $_GET['argumentaire'], PDO::PARAM_INT);
    $query->execute();
        
}
?>