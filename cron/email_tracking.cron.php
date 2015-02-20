<?php
    
// On charge l'API Mandrill
$mandrill = Configuration::read('mail');

// on recherche tous les emails envoyés 
$link = Configuration::read('db.link');
$query = $link->prepare('SELECT * FROM `tracking` WHERE `tracking_status` = "queued"');
$query->execute();
$datas = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $link->prepare('UPDATE `tracking` SET `tracking_opens` = :opens, `tracking_status` = :status WHERE `tracking_id` = :id');

foreach ($datas as $data) {
    $result = $mandrill->messages->info($data['tracking_id']);
    
    $query->bindParam(':opens', $result['opens']);
    $query->bindParam(':status', $result['state']);
    $query->bindParam(':id', $result['_id']);
    $query->execute();
}
