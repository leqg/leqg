<?php

    // On récupère les informations envoyées
    $infos = $_POST;
    
    // On enregistre les informations
if ($infos['type'] == 'boitage') {
    $boitage->reporting($infos['mission'], $infos['id'], $infos['statut']);
} else if ($infos['type'] == 'porte') {
    $porte->reporting($infos['mission'], $infos['id'], $infos['statut']);
} else {
    //$boitage->reporting();
}

?>