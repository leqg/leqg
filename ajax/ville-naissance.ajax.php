<?php
	// On récupère les données POST
	$ville = $_POST['ville'];
	
	// On effectue la recherche
	$villes = $carto->recherche_ville($ville);
	
	foreach ($villes as $ville) :
?>
	<li class="ville propositionVilleNaissance cursor" data-ville="<?php echo $ville['id']; ?>" data-nom="<?php echo $ville['nom']; ?> (<?php $carto->afficherDepartement($ville['departement_id']); ?>)">
		<strong><?php echo $ville['nom']; ?> (<?php $carto->afficherDepartement($ville['departement_id']); ?>)</strong>
	</li>
<?php endforeach; ?>