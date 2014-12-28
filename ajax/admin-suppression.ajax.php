<?php 
    if (isset($_GET['compte']) || isset($_POST['compte'])) {
        $compte = (isset($_GET['compte'])) ? $_GET['compte'] : $_POST['compte'];
        
        // On essaye de récupérer des informations
        $infos = User::infos($compte);
        
        if ($infos) {
    		$link = Configuration::read('db.core');
            $client = Configuration::read('ini')['LEQG']['compte'];

    		$query = $link->prepare('DELETE FROM `compte` WHERE `id` = :id AND `client` = :client');
    		$query->bindParam(':client', $client);
    		$query->bindParam(':id', $compte);
    		$query->execute();

    		Core::tpl_go_to('administration', true);
        } else {
            http_response_code(418);
        }
    } else {
        http_response_code(418);
    }
?>