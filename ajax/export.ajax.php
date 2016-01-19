<?php
/**
 * Réalisation d'un export de contacts selon un tri envoyé
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$var = $_GET;
$var['criteres'] = trim($var['criteres'], ';');
$contacts = People::listing($var, 0, false);

$fichier = array();

$nomFichier = 'export-' . User::ID() . '-' . uniqid() . '.csv';
$file = fopen('exports/' . $nomFichier, 'w+');

$entete = [
    'nom',
    'nom_usage',
    'prenoms',
    'sexe',
    'date_naissance',
    'age',
    'adresse declaree',
    'adresse electorale',
    'bureau',
    'ville',
    'electeur',
    'electeur_europeen',
    'electeur_municipales',
    'organisme',
    'fonction',
    'tags'
];

fputcsv($file, $entete, ';', '"');

foreach ($contacts as $_contact) {
    $contact = new People($_contact);
    $address = $contact->postal_address();
    $poll = Maps::poll_data($contact->get('bureau'));
    $birthdate = new DateTime($contact->get('date_naissance'));

    $_fichier = array(
        $contact->get('nom'),
        $contact->get('nom_usage'),
        $contact->get('prenoms'),
        $contact->get('sexe'),
        $birthdate->format('d/m/Y'),
        $contact->age(),
        $address['reel'],
        $address['officiel'],
        $poll['number'],
        $poll['city'],
        $contact->get('electeur'),
        $contact->get('electeur_europeen'),
        $contact->get('electeur_municipales'),
        $contact->get('organisme'),
        $contact->get('fonction'),
        implode(',', $contact->get('tags'))
    );

    fputcsv($file, $_fichier, ';', '"');
}

// On retraite le nom du fichier
$f = 'exports/' . $nomFichier;

if ($f) {
    $email = file_get_contents('tpl/mail/export-reussi.tpl.html');
    $objet = '[LeQG] Votre export est prêt à être téléchargé';
    $email = strtr(
        $email,
        array('{URL}' => 'http://'.Configuration::read('url').$f)
    );
} else {
    $email = file_get_contents('tpl/mail/export-echec.tpl.html');
    $objet = '[LeQG] Votre export a provoqué un erreur';
}

$query = Core::query('user-data', 'core');
$query->bindValue(':user', User::ID());
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);

$service = Configuration::read('mail');
$to = array(
    array(
        'email' => $data['email'],
        'name' => $data['firstname'].' '.$data['lastname'],
        'type' => 'to'
    )
);

$mail = array(
    'html' => $email,
    'subject' => $objet,
    'from_email' => 'serveur@leqg.info',
    'from_name' => 'LeQG.info',
    'to' => $to,
    'headers' => array('Reply-To' => 'tech@leqg.info'),
    'track_opens' => true,
    'auto_text' => true
);
$async = true;
$service->messages->send($mail, $async);
