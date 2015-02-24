<?php
$building = Maps::building_data($_POST['immeuble']);
$street = Maps::street_data($building['street']);
$zipcode = Maps::zipcode_detect($street['id']);
$city = Maps::city_data($street['city']);
$address = Maps::address_new($_POST['contact'], $city['id'], $zipcode, $street['id'], $building['id']);
$data = new People($_POST['contact']);
$postal = $data->postal_address();
echo $postal['reel'];
