<?php
/**
 * Envoi d'une campagne de SMS
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$campagne = array(
    'titre' => $_GET['titre'],
    'message' => $_GET['message']
);

$campaign = Campaign::create('sms');
$campaign = new Campaign($campaign);

$campaign->update('titre', $campagne['titre']);
$campaign->update('message', $campagne['message']);

$var = $_GET;
$var['criteres'] = trim($var['criteres'], ';');
$campaign->recipients_add(People::listing($var, 0, false));

echo $campaign->get('id');
