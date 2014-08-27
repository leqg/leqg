<?php
	// On récupère la ville recherchée et on la formate
	$rue = $core->formatage_recherche($_POST['rue']);
	$ville = $_POST['ville'];
	
	
	// On fait la recherche
	$rues = $carto->recherche_rue($ville, $rue);
	
	// On fait la liste des résultats
	foreach ($rues as $rue) :
?>
	<a href="<?php $core->tpl_go_to('porte', array('action' => 'nouveau', 'ville' => $ville, 'rue' => $rue['id'])); ?>">
		<li class="rue">
			<strong><?php $carto->afficherRue($rue['id']); ?></strong>
			<p><?php $carto->afficherVille($rue['commune_id']); ?></p>
		</li>
	</a>
<?php endforeach; ?>