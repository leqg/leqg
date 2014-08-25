<?php
	// On récupère la ville recherchée avant de renvoyer les options demandées pour le formulaire ou les puces pour la liste
	$canton = $_POST['canton'];
	$bureau = $_POST['bureau'];

	// On exécute la recherche qui correspond
	$cantons = $carto->recherche_canton($canton);
	
	// On vérifie qu'il y a bien une réponse existante
	if (count($cantons)) :
		foreach ($cantons as $canton) :
?>
	<a href="ajax.php?script=modifier-canton&bureau=<?php echo $bureau; ?>&canton=<?php echo $canton['id']; ?>">
		<li class="canton">
			<strong><?php echo $canton['nom']; ?></strong>
			<p><?php $carto->afficherDepartement(substr($canton['id'], 0, 2)); ?>&nbsp;&nbsp;-&nbsp;&nbsp;Arrondissement <em><?php $carto->afficherArrondissement($canton['arrondissement_id']); ?></em>
			</p>
		</li>
	</a>
<?php endforeach; else : ?>
	<li class="vide">
		<strong>Aucun canton correspondant à votre recherche</strong>
	</li>
<?php endif; ?>