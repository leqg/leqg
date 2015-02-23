<?php
$street = Maps::street_data($_POST['rue']);
$building = Maps::building_new($_POST['immeuble'], $street['id']);
$zipcode = Maps::zipcode_detect($street['id']);
$city = Maps::city_data($street['city']);
$address = Maps::address_new($_POST['fiche'], $city['id'], $zipcode, $street['id'], $building);
$data = new People($_POST['fiche']);
$postal = $data->postal_address();
echo $postal['reel'];
