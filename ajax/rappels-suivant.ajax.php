<?php
    // On sauvegarde les données envoyées
if (isset($_GET['contact'], $_GET['argumentaire'], $_GET['reporting'])) {
        
    // On se connecte à la BDD
    $link = Configuration::read('db.link');
    $userId = User::ID();
        
    // On exécute la requête de fermeture de cet appel
    $query = $link->prepare('UPDATE `rappels` SET `rappel_statut` = :statut WHERE `contact_id` = :contact AND `argumentaire_id` = :argumentaire');
    $query->bindParam(':contact', $_GET['contact'], PDO::PARAM_INT);
    $query->bindParam(':statut', $_GET['reporting'], PDO::PARAM_INT);
    $query->bindParam(':argumentaire', $_GET['argumentaire'], PDO::PARAM_INT);
    $query->execute();
        
    // On cherche les infos sur les rappels
    $query = $link->prepare('SELECT * FROM `rappels` WHERE `argumentaire_id` = :argumentaire AND `contact_id` = :contact');
    $query->bindParam(':contact', $_GET['contact'], PDO::PARAM_INT);
    $query->bindParam(':argumentaire', $_GET['argumentaire'], PDO::PARAM_INT);
    $query->execute();
    $appel = $query->fetch(PDO::FETCH_ASSOC);
        
    // On cherche les infos sur l'argumentaire
    $query = $link->prepare('SELECT * FROM `argumentaires` WHERE `argumentaire_id` = :argumentaire');
    $query->bindParam(':argumentaire', $_GET['argumentaire'], PDO::PARAM_INT);
    $query->execute();
    $argumentaire = $query->fetch(PDO::FETCH_ASSOC);
        
    // On prépare le message d'accompagnement
    $message = array(
     2 => '',
     3 => ' - Donne procuration',
     4 => ' - Souhaite être contacté'
    );
        
    $objet = 'Rappel ' . $argumentaire['argumentaire_nom'] . $message[$_GET['reporting']];
        
    // On enregistre l'élément d'historique
    $query = $link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`, `historique_objet`, `historique_notes`, `campagne_id`) VALUES (:contact, :compte, "appel", NOW(), :objet, :notes, :campagne)');
    $query->bindParam(':contact', $_GET['contact'], PDO::PARAM_INT);
    $query->bindParam(':compte', $userId, PDO::PARAM_INT);
    $query->bindParam(':objet', $objet);
    $query->bindParam(':notes', $appel['rappel_reporting']);
    $query->bindParam(':campagne', $argumentaire['argumentaire_id'], PDO::PARAM_INT);
    $query->execute();
}
?>