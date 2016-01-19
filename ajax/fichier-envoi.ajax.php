<?php
if (isset($_POST['fiche'], $_POST['evenement'], $_FILES['formFichier'], $_POST['formFichierTitre'], $_POST['formFichierDesc'])) {
    // On récupère les informations
    $fiche = $_POST['fiche'];
    $evenement = $_POST['evenement'];
    $fichier = $_FILES['formFichier'];
    $titre = $_POST['formFichierTitre'];
    $description = $_POST['formFichierDesc'];
        
    // On développe un tableau des données à insérer en SQL
    $data = array(
    'evenement' => $evenement,
    'titre' => $titre,
    'description' => $description
    );
        
    // On ouvre la fiche contact
    $contact = new People($fiche);
        
    // On lance la gestion du fichier envoyé
    $contact->file_upload($fichier, $data);
        
    // On réoriente l'utilisateur
    Core::tpl_go_to('contact', array('contact' => $fiche, 'evenement' => $evenement), true);
}
else
{
    echo 'error';
}
?>