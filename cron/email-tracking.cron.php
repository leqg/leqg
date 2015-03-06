<?php
    
// On charge l'API Mandrill
$mandrill = Configuration::read('mail');

// on recherche tous les emails envoyés 
$link = Configuration::read('db.link');
$query = $link->prepare('SELECT * FROM `tracking` WHERE `status` != "pending" AND `control` = 0 ORDER BY `time` ASC LIMIT 0, 150');
$query->execute();
$datas = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $link->prepare('UPDATE `tracking` SET `opens` = :opens, `status` = :status, `control` = 1 WHERE `id` = :id');

foreach ($datas as $data) {
    $result = $mandrill->messages->info($data['id']);

    switch($result['state']) {
        case 'deferred':
            $result['state'] = 'queued';
            break;
        
        case 'hard-bounced':
            $result['state'] = 'rejected';
            break;
        
        case 'soft-bounced':
            $result['state'] = 'rejected';
            break;
        
        case 'bounced':
            $result['state'] = 'rejected';
            break;
    }

    $query->bindParam(':opens', $result['opens']);
    $query->bindParam(':status', $result['state']);
    $query->bindParam(':id', $result['_id']);
    $query->execute();
    
    if ($result['state'] == 'rejected') {
        $query2 = $link->prepare('UPDATE `tracking` SET `reject_reason` = :reason, `reject_msg` = :msg WHERE `id` = :id');
        $query2->bindValue(':reason', $result['bounce_description']);
        $query2->bindValue(':msg', $result['diag']);
        $query2->bindValue(':id', $result['_id']);
        $query2->execute();
    }
}
