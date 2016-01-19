<?php
    // On ouvre la mission
    $data = new Mission($_GET['code']);
    
    // On vérifie que la mission a bien été ouverte
if ($data->err) { Core::tpl_go_to('porte', true); 
}

    // On récupère les données du formulaire
    $reporting = $_POST;
    
    $contact = new Contact(md5($_GET['electeur']));
    
    // On enregistre l'adresse mail si elle existe
if (isset($reporting['email']) && !empty($reporting['email'])) {
    $contact->ajoutCoordonnees('email', $reporting['email']);
}
    
    // On regarde le type de numéro de téléphone puis on l'enregistre s'il existe
if (isset($reporting['phone']) && !empty($reporting['phone'])) {
    $numero = preg_replace('`[^0-9]`', '', $reporting['phone']);
    $premiersNums = $numero{0}.$numero{1};
        
    if ($premiersNums == 06 || $premiersNums == 07) {
            $contact->ajoutCoordonnees('mobile', $numero);
    } else {
            $contact->ajoutCoordonnees('fixe', $numero);
    }
}
    
    Core::tpl_go_to('mission', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble']), true);
?>
