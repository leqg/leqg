<?php 
if (isset($_GET['compte'], $_GET['lvl']) || isset($_POST['compte'], $_POST['lvl'])) {
    $compte = (isset($_GET['compte'])) ? $_GET['compte'] : $_POST['compte'];
    $lvl = (isset($_GET['lvl'])) ? $_GET['lvl'] : $_POST['lvl'];
        
    // On essaye de récupérer des informations
    $infos = User::data($compte);
        
    if ($infos) {
        $link = Configuration::read('db.core');
        $client = Configuration::read('client');
            
        $query = $link->prepare('UPDATE `user` SET `auth_level` = :auth WHERE `id` = :id AND `client` = :client');
        $query->bindParam(':auth', $lvl);
        $query->bindParam(':client', $infos['client']);
        $query->bindParam(':id', $infos['id']);
        $query->execute();
    
        Core::tpl_go_to('administration', array('compte' => $compte), true);
    } else {
        http_response_code(418);
    }
} else {
    http_response_code(418);
}
?>