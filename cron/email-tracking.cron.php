<?php
    
// On charge l'API Mandrill
$mandrill = Configuration::read('mail');

// on recherche tous les emails envoyés 
$link = Configuration::read('db.link');
$query = $link->prepare('SELECT * FROM `tracking` WHERE `id` != "" AND `control` = 0 ORDER BY `time` ASC LIMIT 0, 150');
$query->execute();
$datas = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $link->prepare('UPDATE `tracking` SET `opens` = :opens, `status` = :status, `control` = 1 WHERE `id` = :id');

foreach ($datas as $data) {
    $result = $mandrill->messages->info($data['id']);

    $query->bindParam(':opens', $result['opens']);
    $query->bindParam(':status', $result['state']);
    $query->bindParam(':id', $result['_id']);
    $query->execute();
}
