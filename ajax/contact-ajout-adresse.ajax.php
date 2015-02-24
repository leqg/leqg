<?php
$rue = Maps::street_new($_POST['rue'], $_POST['ville']);
$immeuble = Maps::building_new($_POST['immeuble'], $rue);
$zipcode = Maps::zipcode_new($_POST['zipcode'], $_POST['ville']);
$adresse = Maps::address_new($_POST['fiche'], $_POST['ville'], $zipcode, $rue, $immeuble);
$data = new People($_POST['fiche']);
$postal = $data->postal_address();
echo $postal['reel'];