<?php
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
