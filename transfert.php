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
    
    $adresse = array(
        'pays' => '',
        'ville' => '',
        'zip' => '',
        'street' => '',
        'building' => ''
    );
    
    // On cherche le numéro
    if (!empty($contact['ADR2'])) {
        $adresse['building'] = $contact['ADR2'];
    } elseif (is_numeric(substr($contact['ADR1'], 0, 1))) {
        $num_rue = explode(' ', $contact['ADR1']);
        $adresse['building'] = $num_rue[0];
        unset($num_rue[0]);
        $adresse['street'] = implode(' ', $num_rue);
    } elseif (is_numeric(substr($contact['ADR1'], -1, 1))) {
        $num_rue = explode(' ', $contact['ADR1']);
        $adresse['building'] = $num_rue[count($num_rue)-1];
        unset($num_rue[count($num_rue)-1]);
        $adresse['street'] = implode(' ', $num_rue);
    } else {
        $adresse['street'] = $contact['ADR1'];
        $adresse['building'] = null;
    }

    // On cherche le code postal
    if (empty($contact['ADR7'])) {
        $cp_ville = explode(' ', $contact['ADR5']);
        $adresse['zip'] = $cp_ville[0];
        unset($cp_ville[0]);
        $adresse['ville'] = implode(' ', $cp_ville);
        $adresse['pays'] = $contact['ADR6'];
    } else {
        $cp_ville = explode(' ', $contact['ADR6']);
        $adresse['zip'] = $cp_ville[0];
        unset($cp_ville[0]);
        $adresse['ville'] = implode(' ', $cp_ville);
        $adresse['pays'] = $contact['ADR7'];
    }
    
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