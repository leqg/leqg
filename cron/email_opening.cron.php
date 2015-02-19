<?php
    
// On charge l'API Mandrill
$mandrill = Configuration::read('mail');

// on recherche tous les emails envoyés 
$link = Configuration::read('db.link');
$query = $link->prepare('SELECT * FROM `historique` WHERE `historique_tracking_id` != "" ORDER BY `historique_timestamp` ASC');
$query->execute();
$datas = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $link->prepare('UPDATE `historique` SET `historique_tracking_opens` = :opens, `historique_tracking_status` = :status WHERE `historique_tracking_id` = :id');

foreach ($datas as $data) {
    $result = $mandrill->messages->info($data['historique_tracking_id']);
    
    $query->bindParam(':opens', $result['opens']);
    $query->bindParam(':status', $result['state']);
    $query->bindParam(':id', $result['_id']);
    $query->execute();
}
