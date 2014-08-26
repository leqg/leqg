<?php
	// On récupère la ville recherchée et on la formate
	$ville = $core->formatage_recherche($_POST['ville']);
	
	// On fait la recherche
	$villes = $carto->recherche_ville($ville);
	
	// On fait la liste des résultats
	foreach ($villes as $ville) :
?>
	<a href="<?php $core->tpl_go_to('carto', array('module' => 'export', 'criteresGeographiques' => 'true', 'ville' => $ville['id'])); ?>">
		<li class="ville">
			<strong><?php $carto->afficherVille($ville['id']); ?></strong>
			<p><?php $carto->afficherDepartement($ville['departement_id']); ?></p>
		</li>
	</a>
<?php endforeach; ?>