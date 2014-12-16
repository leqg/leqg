<?php
    if (isset($_POST['mission']))
    {
        // On ouvre la fiche concernée
        $link = Configuration::read('db.link');
        
        // On effectue la modification
        $query = $link->prepare('UPDATE `mission` SET `mission_statut` = 0 WHERE `mission_id` = :id');
        $query->bindParam(':id', $_POST['mission'], PDO::PARAM_INT);
        $query->execute();
        
        // On redirige vers les dossiers
        Core::tpl_go_to('porte', true);
    }
?>