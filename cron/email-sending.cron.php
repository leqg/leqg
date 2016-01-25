<?php
/**
 * Sending email cron job
 *
 * PHP version 5
 *
 * @category Cron
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$query = Core::query('tracking-to-send');
$query->execute();

if ($query->rowCount()) {
    $emails = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($emails as $email) {
        Campaign::sending($email['campaign'], $email['email']);
    }
}
