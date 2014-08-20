<?php

	// on récupère la recherche
	$recherche = $_POST['recherche'];
	
	// On effectue la recherche
	$villes = $carto->recherche_ville($recherche);
	
	// On retourne les différentes villes
	foreach ($villes as $ville) :
?>
	<a class="nostyle" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'ville', 'ville' => $ville['id'])); ?>">
		<li class="ville">
			<strong><?php echo $ville['nom']; ?> (<?php $carto->afficherDepartement($ville['departement_id']); ?>)</strong>
		</li>
	</a>
<?php endforeach; ?>