<?php
require_once('includes.php');

$link = Configuration::read('db.link');

$query = $link->prepare('SELECT * FROM `TABLE 47` LIMIT 0, 50');
$query->execute();
$contacts = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($contacts as $contact) {
    // On regarde si un contact correspodant existe déjà
    $nomPrenom = $contact['Prénom'].' '.$contact['Nom'];
    $search = People::search($nomPrenom);
    
    if (count($search)) {
        if (count($search) == 1) {
            $person = new People($search[0]['id']);
        } else {
            $person = People::create();
            $person = new People($person);
        }
    } else {
        $person = People::create();
        $person = new People($person);
    }
    
    // On met à jour l'organisation si elle existe
    if (!empty($contact['Société'])) {
        $person->update('organisme', $contact['Société']);
    }
    
    // On met à jour le titre
    if (!empty($contact['Titre1'])) {
        $person->update('fonction', $contact['Titre1']);
    }
    
    // On rajoute l'adresse
    if (!empty($contact['Ruebureau'])) {
        $adresse = array(
            'numero' => null,
            'rue' => $contact['Ruebureau'],
            'cp' => $contact['Codepostalbureau'],
            'ville' => $contact['Villebureau'],
            'pays' => $contact['PaysRégionbureau'],
        );
        
        $first = substr($adresse['rue'], 0, 1);
        $last = substr($adresse['rue'], -1, 1);
        $elements = explode(' ', $adresse['rue']);
        if (is_numeric($first)) {
            $adresse['numero'] = $elements[0];
            unset($elements[0]);
            $adresse['rue'] = implode(' ', $elements);
        } elseif (is_numeric($last)) {
            $adresse['numero'] = $elements[count($elements)-1];
            unset($elements[count($elements)-1]);
            $adresse['rue'] = implode(' ', $elements);
        }
        
        $address = array(
            'city' => null,
            'zip' => null,
            'street' => null,
            'building' => null
        );
        
        if (!empty($adresse['ville'])) {
            $cities = Maps::city_search($adresse['ville']);
            if (count($countries)) {
                $address['city'] = $cities[0]['id'];
            } else {
                $city = Maps::city_create($adresse['ville']);
                $address['city'] = $city;
            }
        }
        
        if (!empty($adresse['cp'])) {
            $zipcodes = Maps::zipcode_search($adresse['cp']);
            if (count($zipcodes)) {
                $address['zip'] = $zipcodes[0]['id'];
            } else {
                $zipcode = Maps::zipcode_new($adresse['cp'], $address['city']);
                $address['zip'] = $zipcode;
            }
        }
        
        if (!empty($adresse['rue'])) {
            $streets = Maps::street_search($adresse['rue'], $address['city']);
            if (count($streets)) {
                $address['street'] = $streets[0]['id'];
            } else {
                $street = Maps::street_create($adresse['rue'], $address['city']);
                $address['street'] = $street;
            }
        }
        
        if (!empty($adresse['numero']) && !is_null($adresse['numero'])) {
            $buildings = Maps::building_search($adresse['numero'], $address['street']);
            if (count($buildings)) {
                $address['building'] = $buildings[0]['id'];
            } else {
                $building = Maps::building_new($adresse['numero'], $address['street']);
                $address['building'] = $building;
            }
        }
        
        Maps::address_new($person->get('id'), $address['city'], $address['zip'], $address['street'], $address['building'], 'reel');
    }
    
    // On rajoute les coordonnées téléphoniques
    if (!empty($contact['Téléphonebureau'])) $person->contact_details_add($contact['Téléphonebureau'], 'fixe');
    if (!empty($contact['Téléphone2bureau'])) $person->contact_details_add($contact['Téléphone2bureau'], 'fixe');
    if (!empty($contact['Téléphonedomicile'])) $person->contact_details_add($contact['Téléphonedomicile'], 'fixe');
    if (!empty($contact['Téléphone2domicile'])) $person->contact_details_add($contact['Téléphone2domicile'], 'fixe');
    if (!empty($contact['Télmobile'])) $person->contact_details_add($contact['Télmobile'], 'mobile');

    if (!empty($contact['Adressedemessagerie'])) $person->contact_details_add($contact['Adressedemessagerie'], 'email');
    if (!empty($contact['Adressedemessagerie2'])) $person->contact_details_add($contact['Adressedemessagerie2'], 'email');
    if (!empty($contact['Adressedemessagerie3'])) $person->contact_details_add($contact['Adressedemessagerie3'], 'email');


    $person->tag_add('contacts');

    
    $query = $link->prepare('DELETE FROM `TABLE 47` WHERE `id` = :id');
    $query->bindValue(':id', $contact['id'], PDO::PARAM_INT);
    $query->execute();
}

$query = $link->query('SELECT COUNT(*) FROM `TABLE 47`');
$nb = $query->fetch(PDO::FETCH_NUM);
if ($nb[0]) :
?>
<script>
    var url = 'importation.php';
    document.location.href = url;
</script>
<?php else: echo 'Fini!'; endif; ?>