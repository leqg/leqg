<?php
require_once 'includes.php';

$link = Configuration::read('db.link');

$query = $link->prepare('SELECT * FROM `TABLE 30` LIMIT 0, 50');
$query->execute();
$contacts = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($contacts as $contact) {
    $person = People::create();
    $person = new People($person);
    Core::debug($contact, false);

    // Traitement du nom
    $person->update('nom', $contact['NOM']);
    $person->update('prenoms', $contact['PRENOM']);
    
    // On paramètre le sexe
    $genre = trim($contact['GENRE']);
    if ($genre == 'Madame') {
        $person->update('sexe', 'F');
    } else {
        $person->update('sexe', 'H');
    }
    
    $adresse = array(
        'pays' => 'France',
        'ville' => '',
        'zip' => '',
        'street' => '',
        'building' => ''
    );
    
    $decomposition_rue = explode(' ', $contact['ADRESSE 3']);
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
    $cp_ville = explode(' ', $contact['VILLE']);
    $adresse['zip'] = $cp_ville[0];
    unset($cp_ville[0]);
    $adresse['ville'] = mb_convert_case(trim(implode(' ', $cp_ville)), MB_CASE_TITLE);
    
    if (empty($adresse['pays'])) { $adresse['pays'] = null; 
    }
    if (empty($adresse['ville'])) { $adresse['ville'] = null; 
    }
    if (empty($adresse['zip'])) { $adresse['zip'] = null; 
    }
    if (empty($adresse['street'])) { $adresse['street'] = null; 
    }
    if (empty($adresse['building'])) { $adresse['building'] = null; 
    }
    
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
    Maps::address_new($person->get('id'), $address['ville'], $address['zip'], $address['street'], $address['building'], 'reel');
    
    $person->contact_details_add($contact['MAIL']);
    $person->tag_add('Sénateur PS');
    $person->tag_add($contact['REGION']);
    
    $query = $link->prepare('DELETE FROM `TABLE 30` WHERE `id` = :id');
    $query->bindValue(':id', $contact['id'], PDO::PARAM_INT);
    $query->execute();
}

$query = $link->query('SELECT COUNT(*) FROM `TABLE 30`');
$nb = $query->fetch(PDO::FETCH_NUM);
if ($nb[0]) :
?>
<script>
    var url = 'transfert.php';
    document.location.href = url;
</script>
<?php else: echo 'Fini!'; 
endif; ?>