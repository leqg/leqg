<?php
    // On ouvre la mission
    $data = new Mission($_GET['code']);
    
    // On vérifie que la mission a bien été ouverte
if ($data->err) { Core::goPage('porte', true); 
}
    
    // On récupère tous les items de l'immeuble et la rue en question et la ville concernée
    $rue = Carto::rue($_GET['rue']);
    $immeuble = Carto::immeuble($_GET['immeuble']);
    
    // typologie
    $typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
    Core::loadHeader();
?>

	<h2>Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

    <h3 style="margin: 20px;"><?php echo $immeuble['immeuble_numero']; ?> <?php echo $rue['rue_nom']; ?></h3>

    <a href="<?php Core::goPage('report', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'], 'electeur' => $_GET['immeuble'], 'statut' => 1)); ?>" class="nostyle bouton">Inaccessible</a>
    <a href="<?php Core::goPage('report', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'], 'electeur' => $_GET['immeuble'], 'statut' => 2)); ?>" class="nostyle bouton">Boîtage réalisé</a>
	
	<a href="<?php Core::goPage('mission', array('code' => $_GET['code'], 'rue' => $_GET['rue'])); ?>" class="bouton nostyle">Retour à la rue</a>

<?php Core::loadFooter(); ?>