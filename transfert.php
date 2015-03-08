<?php
require_once('includes.php');

$link = Configuration::read('db.link');

$query = $link->prepare('SELECT * FROM `TABLE 46` LIMIT 0, 50');
$query->execute();
$contacts = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($contacts as $contact) {
    $person = People::create();
    $person = new People($person);
    Core::debug($contact, false);
    // Traitement du bureau de vote
    $polls = Maps::poll_search($contact['Bureau']);
    if (count($polls)) {
        $person->update('bureau', $polls[0]['id']);
    } else {
        $poll = Maps::poll_create($contact['Bureau'], "", 1);
        $person->update('bureau', $poll);
    }

    // Traitement du nom
    $prenoms = str_replace(',', '', $contact['Prénoms']);
    $person->update('nom', $contact['Nom']);
    $person->update('nom_usage', $contact['Nom d\'usage']);
    $person->update('prenoms', mb_convert_case($prenoms, MB_CASE_TITLE));
    
    // On calibre le fait qu'il s'agit d'un électeur
    $person->update('electeur', 1);
    
    // On paramètre le sexe
    if ($contact['Sexe'] == 'F') {
        $person->update('sexe', 'F');
    } else {
        $person->update('sexe', 'H');
    }
    
    // Traitement de la naissance
    $date_naissance = DateTime::createFromFormat('d/m/Y', $contact['Date de naissance']);
    $person->update('date_naissance', $date_naissance->format('Y-m-d'));
    
    $adresse = array(
        'pays' => 'France',
        'ville' => '',
        'zip' => '',
        'street' => '',
        'building' => ''
    );
    
    $decomposition_rue = explode(' ', $contact['Adresse 1']);
    $numero = $decomposition_rue[0];
    $first = substr($numero, 0, 1);
    
    if (is_numeric($first)) {
        $adresse['building'] = $numero;
        unset($decomposition_rue[0]);
    } else {
        $adresse['building'] = null;
    }
    
    $adresse['street'] = mb_convert_case(implode(' ', $decomposition_rue), MB_CASE_TITLE);

    // On cherche le code postal
    $cp_ville = explode(' ', $contact['Adresse 4']);
    $adresse['zip'] = $cp_ville[0];
    unset($cp_ville[0]);
    $adresse['ville'] = mb_convert_case(implode(' ', $cp_ville), MB_CASE_TITLE);
    
    if (empty($adresse['pays'])) $adresse['pays'] = null;
    if (empty($adresse['ville'])) $adresse['ville'] = null;
    if (empty($adresse['zip'])) $adresse['zip'] = null;
    if (empty($adresse['street'])) $adresse['street'] = null;
    if (empty($adresse['building'])) $adresse['building'] = null;
    
    $address = array(
        'pays' => '',
        'ville' => '',
        'zip' => '',
        'street' => '',
        'building' => ''
    );
    
    if (!is_null($adresse['pays'])) {
        $countries = Maps::country_search($adresse['pays']);
        if (count($countries)) {
            $address['pays'] = $countries[0]['id'];
        } else {
            $country = Maps::country_create($adresse['pays']);
            $address['pays'] = $country;
        }
    } else {
        $address['pays'] = null;
    }
    
    if (!is_null($adresse['ville'])) {
        $city = Maps::city_search($adresse['ville'], $address['pays']);
        if (count($city)) {
            $address['ville'] = $city[0]['id'];
        } else {
            $city = Maps::city_create($adresse['ville'], $address['pays']);
            $address['ville'] = $city;
        }
    } else {
        $address['ville'] = null;
    }
    
    if (!is_null($adresse['zip'])) {
        $zipcode = Maps::zipcode_search($adresse['zip'], $address['ville']);
        if (count($zipcode)) {
            $address['zip'] = $zipcode[0]['id'];
        } else {
            $zipcode = Maps::zipcode_new($adresse['zip'], $address['ville']);
            $address['zip'] = $zipcode;
        }
    } else {
        $address['zip'] = null;
    }
    
    if (!is_null($adresse['street'])) {
        $street = Maps::street_search($adresse['street'], $address['ville']);
        if (count($street)) {
            $address['street'] = $street[0]['id'];
        } else {
            $street = Maps::street_create($adresse['street'], $address['ville']);
            $address['street'] = $street;
        }
    } else {
        $address['street'] = null;
    }
    
    if (!is_null($adresse['building'])) {
        $building = Maps::building_search($adresse['building'], $address['street']);
        if (count($building)) {
            $address['building'] = $building[0]['id'];
        } else {
            $building = Maps::building_new($adresse['building'], $address['street']);
            $address['building'] = $building;
        }
    } else {
        $address['building'] = null;
    }
    
    // On lance la création de l'adresse
    Maps::address_new($person->get('id'), $address['ville'], $address['zip'], $address['street'], $address['building'], 'officiel');
    
    $query = $link->prepare('DELETE FROM `TABLE 46` WHERE `id` = :id');
    $query->bindValue(':id', $contact['id'], PDO::PARAM_INT);
    $query->execute();
}

$query = $link->query('SELECT COUNT(*) FROM `TABLE 46`');
$nb = $query->fetch(PDO::FETCH_NUM);
if ($nb[0]) :
?>
<script>
    var url = 'transfert.php';
    document.location.href = url;
</script>
<?php else: echo 'Fini!'; endif; ?>