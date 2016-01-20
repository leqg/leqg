<?php
    // On ouvre la mission
    $data = new Mission($_GET['mission']);
    
    // On vérifie que la mission a bien été ouverte
if ($data->err) { Core::goTo('porte', true); 
}

    // On récupère les données du formulaire
    $reporting = $_POST;
    
    // S'il s'agit d'un porte à porte
if ($data->get('mission_type') == 'porte') {
    // On transforme les données pour avoir un tableau electeur => statut
    $coordonnees = array();
    foreach ($reporting as $report => $statut) {
        // On récupère l'identifiant de l'électeur
        $electeur = explode('-', $report);
            
        // On retraite l'enregistrement
        $reporting[$electeur[1]] = $statut;
            
        // On supprime l'ancien enregistrement
        unset($reporting[$report]);
            
        // Si c'est une demande de recontact, on le laisse dans un tableau de côté pour récupérer les coordonnées
        if ($statut == 4) {
            $coordonnees[] = $electeur[1];
        }
    }
        
    // Pour chaque report, on l'enregistre dans la base de données
    foreach ($reporting as $report => $statut) {
        $data->reporting($report, $statut);
    }
        
    // S'il n'y a pas de coordonnées à récupérer, on redirige
    if (!count($coordonnees)) { Core::goTo('reporting', array('mission' => $_GET['mission'], 'rue' => $_GET['rue']), true); 
    }
}
    
    // S'il s'agit d'un boîtage
else {
    // On transforme les données pour avoir un tableau electeur => statut
    foreach ($reporting as $report => $statut) {
        // On récupère l'identifiant de l'électeur
        $electeur = explode('-', $report);
            
        // On retraite l'enregistrement
        $reporting[$electeur[1]] = $statut;
            
        // On supprime l'ancien enregistrement
        unset($reporting[$report]);
    }
        
    // Pour chaque report, on l'enregistre dans la base de données
    foreach ($reporting as $report => $statut) {
        $data->reporting($report, $statut);
    }
        
    Core::goTo('reporting', array('mission' => $_GET['mission'], 'rue' => $_GET['rue']), true);
}
    

    // On charge le header
    Core::loadHeader();
?>
<a href="<?php Core::goTo('reporting', array('mission' => $data->get('mission_hash'))); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Retour à la mission</button></a>	
<h2 id="titre-mission" class="titre" data-mission="<?php echo $data->get('mission_hash'); ?>">Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

<form action="<?php Core::goTo('reporting', array('action' => 'coordonnees', 'mission' => $_GET['mission'], 'rue' => $_GET['rue'])); ?>" method="post">
    <section class="contenu">
        <h4>Demande d'informations complémentaires</h4>
        
        <table class="reporting">
	        <thead>
		        <tr>
    		        <th>Électeur</th>
    		        <th style="width: 300px">Email</th>
    		        <th style="width: 200px">Téléphone</th>
		        </tr>
	        </thead>
	        <tbody>
            <?php foreach ($coordonnees as $electeur) : $contact = new People($electeur); ?>
		        <tr class="ligne-electeur-<?php echo $contact->get('id'); ?>">
    		        <td><?php echo $contact->display_name(); ?></td>
    		        <td><input type="text" name="email-<?php echo $electeur; ?>" placeholder="email" style="border: none; width: 100%; text-align: center;"></td>
    		        <td><input type="text" name="phone-<?php echo $electeur; ?>" placeholder="numéro" style="border: none; width: 100%; text-align: center;"></td>
		        </tr>
            <?php endforeach; ?>
	        </tbody>
        </table>
        <button type="submit" style="font-size: 1em;">Enregistrer les modifications</button>
    </section>
</form>