<?php
$campagne = array(
    'titre' => $_GET['titre'],
    'message' => $_GET['message']
);

$campaign = Campaign::create('publi');
$campaign = new Campaign($campaign);

$campaign->update('titre', $campagne['titre']);
$campaign->update('message', $campagne['message']);

$var = $_GET;
$var['criteres'] = trim($var['criteres'], ';');
$contacts = People::listing($var, 0, false);

$fichier = array();
$nomFichier = 'publi-'.md5($campaign->get('id')).'.csv';
$file = fopen('exports/'.$nomFichier, 'w+');

$entete = array(
    'nom',
    'nom_usage',
    'prenoms',
    'numero',
    'rue',
    'cp',
    'ville',
    'pays',
    'origine'
);

fputcsv($file, $entete, ';', '"');

foreach ($contacts as $contact) {
    $person = new People($contact);
    $address = $person->postal_array();
    
    if (isset($address['reel'])) {
        $address = $address['reel'];
        $origine = 'declaree';
    } else {
        $address = $address['officiel'];
        $origine = 'liste-electorale';
    }
    
    $_fichier = array(
        $person->get('nom'),
        $person->get('nom_usage'),
        $person->get('prenoms'),
        $address['building'],
        $address['street'],
        $address['zipcode'],
        $address['city'],
        $address['country'],
        $origine
    );
    
    if (fputcsv($file, $_fichier, ';', '"')) {
        $event = Event::create($person->get('id'));
        $event = new Event($event);
        $event->update('historique_type', 'publi');
        $event->update('historique_objet', $campaign->get('titre'));
        $event->update('historique_notes', $campaign->get('message'));
        $event->update('historique_date', date('Y-m-d'));
        $event->update('campagne_id', $campaign->get('id'));
    }
}
