<?php
    // On ouvre la mission
    $data = new Mission($_GET['code']);
    
    // On vérifie que la mission a bien été ouverte
if ($data->err) { Core::tpl_go_to('porte', true); 
}
    
    // On récupère les statistiques sur les militants
    $militants = $data->statistiques_militant();
    
    // On récupère la liste des rues de la mission
    $rues = $data->rues();
    
    // typologie
    $typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
    Core::tpl_header();
?>

	<h2>Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>
	
    <?php if ($rues) : ?>
    	<ul class="listeMissions">
            <?php foreach ($rues as $rue) : $stats = $data->statistique_rue($rue['rue_id']); ?>
    		<a href="<?php Core::tpl_go_to('mission', array('code' => $_GET['code'], 'rue' => $rue['rue_id'])); ?>" class="nostyle">
    			<li class="rue">
    				<h4><?php echo $rue['rue_nom']; ?></h4>
                <?php if ($stats && $stats['proportion']) : ?>
                		<p>Rue réalisée à <?php echo $stats['proportion']; ?>&nbsp;%</p>
                <?php elseif ($stats) : ?>
        				<p>Rue non débutée.</p>
                <?php else : ?>
        				<p>Statistiques indisponibles</p>
                <?php endif; ?>
    			</li>
    		</a>
            <?php endforeach; ?>
    	</ul>
    <?php else : ?>
    	<p>Aucune rue n'a été sélectionnée pour l'instant.</p>
    <?php endif; ?>

<?php Core::tpl_footer(); ?>