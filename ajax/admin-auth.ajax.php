<?php 
    if (isset($_GET['compte'], $_GET['lvl']) || isset($_POST['compte'], $_POST['lvl'])) {
        $compte = (isset($_GET['compte'])) ? $_GET['compte'] : $_POST['compte'];
        $lvl = (isset($_GET['lvl'])) ? $_GET['lvl'] : $_POST['lvl'];
        
        // On essaye de récupérer des informations
        $infos = User::infos($compte);
        
        if ($infos) {
    		$link = Configuration::read('db.core');
            $client = Configuration::read('ini')['LEQG']['compte'];
            
    		$query = $link->prepare('UPDATE `compte` SET `auth_level` = :auth WHERE `id` = :id AND `client` = :client');
    		$query->bindParam(':auth', $lvl);
    		$query->bindParam(':client', $client);
    		$query->bindParam(':id', $compte);
    		$query->execute();

    		Core::tpl_go_to('administration', array('compte' => $compte), true);
        } else {
            http_response_code(418);
        }
    } else {
        http_response_code(418);
    }
?>