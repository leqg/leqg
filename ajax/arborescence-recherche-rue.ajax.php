<?php

	// on récupère la recherche
	$recherche = $_POST['recherche'];
	$ville = $_POST['ville'];
	
	// On effectue la recherche
	$rues = $carto->recherche_rue($ville , $recherche);
	
	// On retourne les différentes villes
	foreach ($rues as $rue) :
?>
	<a class="nostyle" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'rue', 'rue' => $rue['id'])); ?>">
		<li class="rue">
			<strong><?php echo $rue['nom']; ?></strong>
		</li>
	</a>
<?php endforeach; ?>