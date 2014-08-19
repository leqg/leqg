<?php
	// On récupère les données POST
	$ville = $_POST['ville'];
	
	// On effectue la recherche
	$villes = $carto->recherche_ville($ville);
	
	foreach ($villes as $ville) :
?>
	<a href="<?php $core->tpl_go_to('fiche', array('id' => $_POST['fiche'])); ?>&modifierRue=true&ville=<?php echo $ville['id']; ?>" class="nostyle">
		<li class="ville" data-ville="<?php echo $ville['id']; ?>" data-fiche="<?php echo $_POST['fiche']; ?>">
			<strong><?php echo $ville['nom']; ?> (<?php $carto->afficherDepartement($ville['departement_id']); ?>)</strong>
		</li>
	</a>
<?php endforeach; ?>