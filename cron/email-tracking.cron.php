<?php
/**
 * Tracking email cron job
 *
 * PHP version 5
 *
 * @category Cron
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On charge l'API Mandrill
$mandrill = Configuration::read('mail');

// on recherche tous les emails envoyés
$link = Configuration::read('db.link');
$sql = 'SELECT *
        FROM `tracking`
        WHERE `status` NOT IN ("pending", "sent", "rejected")
        OR (`status` = "sent" AND `control` = 0)
        ORDER BY RAND()
        LIMIT 0, 300';
$query = $link->prepare($sql);
$query->execute();
$datas = $query->fetchAll(PDO::FETCH_ASSOC);

$sql = 'UPDATE `tracking`
        SET `opens` = :opens,
            `status` = :status,
            `control` = 1
        WHERE `id` = :id';
$query = $link->prepare($sql);

foreach ($datas as $data) {
    try {
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
            $sql = 'UPDATE `tracking`
                    SET `reject_reason` = :reason,
                        `reject_msg` = :msg
                    WHERE `id` = :id';
            $query2 = $link->prepare($sql);
            $query2->bindValue(':reason', $result['bounce_description']);
            $query2->bindValue(':msg', $result['diag']);
            $query2->bindValue(':id', $result['_id']);
            $query2->execute();
        }
    } catch(Mandrill_Error $e) {
        $sql = 'UPDATE `tracking`
                SET `control` = 1 
                WHERE `id` = :id';
        $query3 = $link->prepare($sql);
        $query3->bindValue(':id', $data['id']);
        $query3->execute();
    }
}
