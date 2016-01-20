<?php
    // On ouvre la mission
    $data = new Mission($_GET['code']);
    
    // On vérifie que la mission a bien été ouverte
if ($data->err) { Core::goTo('porte', true); 
}
    
if (!isset($_GET['electeur'], $_GET['statut'])) { Core::goTo(true); 
}
    
    // On récupère les données du formulaire
    $data->reporting($_GET['electeur'], $_GET['statut']);
    
    // S'il faut des coordonnées, on reste sur la page, sinon on dégage
if ($_GET['statut'] != 4 && $data->get('mission_type') == 'porte') { Core::goTo('mission', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble']), true); 
}
if ($data->get('mission_type') == 'boitage') { Core::goTo('mission', array('code' => $_GET['code'], 'rue' => $_GET['rue']), true); 
}
    
    Core::loadHeader();
?>

	<h2>Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

    <h3 style="margin: 20px;">Comment contacter cet électeur ?</h3>
    
    <form action="<?php Core::goTo('report', array('action' => 'coord', 'code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'], 'electeur' => $_GET['electeur'])); ?>" method="post">
        <p style="text-align: center;">Email</p>
        <input type="email" name="email">
        
        <p style="text-align: center;">Téléphone</p>
        <input type="text" name="phone">
        
        <input type="submit" value="Valider">
    </form>
    
<?php Core::loadFooter(); ?>