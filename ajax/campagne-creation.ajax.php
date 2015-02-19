<?php
// On vérifie la bonne réception des données
if (isset($_POST['objet'], $_POST['message'], $_POST['type'])) {
    // On récupère les données
    $objet = $_POST['objet'];
    $message = $_POST['message'];
    $type = $_POST['type'];
    $user = User::ID();
    
    // On lance la création de l'envoi
    $link = Configuration::read('db.link');
    $query = $link->prepare('INSERT INTO `envois` (`compte_id`, `envoi_type`, `envoi_time`, `envoi_titre`, `envoi_texte`) VALUES (:compte, :type, NOW(), :titre, :texte)');
    $query->bindValue(':compte', $user, PDO::PARAM_INT);
    $query->bindValue(':type', $type);
    $query->bindValue(':titre', $objet);
    $query->bindValue(':texte', $message);
    $query->execute();

} else {
    http_response_code(403);
}
