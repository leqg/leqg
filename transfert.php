<?php
require_once('includes.php');

$link = Configuration::read('db.link');

$query = $link->prepare('SELECT * FROM `imports` LIMIT 0, 50');
$query->execute();
$contacts = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($contacts as $contact) {
    $person = People::create();
    $person = new People($person);
    
    
    // Traitement du bureau de vote
    $polls = Maps::poll_search($contact['LIB_BUREAU_DE_VOTE']);
    if (count($polls)) {
        $person->update('bureau', $polls[0]['id']);
    } else {
        $poll = Maps::poll_create($contact['CODE_BUREAU_VOTE'], $contact['LIB_BUREAU_DE_VOTE']);
        $person->update('bureau', $poll);
    }
    
    
    // Traitement du nom
    $prenoms = str_replace(',', '', $contact['PRENOMS']);
    $person->update('nom', $contact['NOMFAM']);
    $person->update('nom_usage', $contact['NOMUSA']);
    $person->update('prenoms', $prenoms);
    
    // On calibre le fait qu'il s'agit d'un électeur
    $person->update('electeur', 1);
    

    // Traitement de la naissance
    $date_naissance = DateTime::createFromFormat('Ymd', $contact['DATNAI']);
    $person->update('date_naissance', $date_naissance->format('Y-m-d'));
    
    
    // Traitement de l'adresse postale
    $address = array();
    $cpville = explode(' ', $contact['ADR2']);
    $cp = $cpville[0];
    $ville = $cpville[1];
    
    $countries = Maps::country_search($contact['ADR3']);
    if (count($countries)) {
        $address['pays'] = $countries[0]['id'];
    } else {
        $country = Maps::country_create($contact['ADR3']);
        $address['pays'] = $country;
    }
    
    $countries = Maps::city_search($ville);
    if (count($countries)) {
        $address['ville'] = $countries[0]['id'];
    } else {
        $city = Maps::city_create($ville, $address['pays']);
        $address['ville'] = $city;
    }
    
    $zipcodes = Maps::zipcode_search($cp);
    if (count($zipcodes)) {
        $address['zip'] = $zipcodes[0]['id'];
    } else {
        $zipcode = Maps::zipcode_new($cp, $address['ville']);
        $address['zip'] = $zipcode;
    }
    
    $adresse1 = $contact['ADR1'];
    $first = substr($adresse1, 0, 1);
    $last = substr($adresse1, -1, 1);
    
    if (is_numeric($first)) {
        $_address = explode(' ', $adresse1);
        $immeuble = $_address[0];
        unset($_address[0]);
        $rue = implode(' ', $_address);
    } elseif (is_numeric($last)) {
        $_address = explode(' ', $adresse1);
        $count = count($_address);
        $immeuble = $_address[$count - 1];
        unset($_address[$count - 1]);
        $rue = implode(' ', $_address);
    } else {
        $rue = $address1;
        $immeuble = null;
    }
    
    $streets = Maps::street_search($rue, $address['ville']);
    if (count($streets)) {
        $address['street'] = $streets[0]['id'];
    } else {
        $street = Maps::street_create($rue, $address['ville']);
        $address['street'] = $street;
    }
    
    if (!is_null($immeuble)) {
        $buildings = Maps::building_search($immeuble, $address['street']);
        if (count($buildings)) {
            $address['building'] = $buildings[0]['id'];
        } else {
            $building = Maps::building_new($immeuble, $address['street']);
            $address['building'] = $building;
        }
    }
    
    // On lance la création de l'adresse
    Maps::address_new($person->get('id'), $address['ville'], $address['zip'], $address['street'], $address['building'], 'officiel');


    // On lance la récupération de l'adresse email
    if (!empty($contact['MAIL'])) {
        $person->contact_details_add($contact['MAIL']);
    }
    
    $person->tag_add($contact['LIB_CENTRE_LEC']);
    
    $query = $link->prepare('DELETE FROM `imports` WHERE `id` = :id');
    $query->bindValue(':id', $contact['id'], PDO::PARAM_INT);
    $query->execute();
}

$query = $link->query('SELECT COUNT(*) FROM `imports`');
$nb = $query->fetch(PDO::FETCH_NUM);
if ($nb[0]) :
?>
<script>
    var url = 'transfert.php';
    document.location.href = url;
</script>
<?php else: echo 'Fini!'; endif; ?>