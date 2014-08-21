<?php
	
	// On va charger les 10 dernières interactions de la page d'accueil contact
	$dossiers = $dossier->recherche();

?>
<section id="fiche">

	<h3>Dossiers actuellement ouverts au sein du QG</h3>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Accédez aux dossiers</span>
			<ul class="listeEncadree">
				<?php foreach($dossiers as $d) : ?>
				<a class="nostyle" href="<?php $core->tpl_go_to('dossier', array('id' => $d['id'])); ?>">
					<li class="dossier">
						<strong><?php echo $d['nom']; ?></strong>
						<p class="tailleNormale">
							<?php echo $d['description']; ?>
						</p>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	
</section>