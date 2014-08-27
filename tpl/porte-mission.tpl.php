<?php $parcours = $mission->chargement($_GET['mission']); ?>
<section id="fiche">
	<header class="porte">
		<h2>
			<span>Porte-à-porte</span>
			<span>Mission <?php echo $parcours['id']; ?></span>
		</h2>
	</header>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Ville</span>
			<p><?php echo $carto->afficherVille($parcours['ville_id']); ?></p>
		</li>
		<li>
			<span class="label-information">Rue</span>
			<p><?php echo $carto->afficherRue($parcours['rue_id']); ?></p>
		</li>
		<li>
			<ul class="listeEncadree">
				<?php $immeubles = explode(',', $parcours['immeubles']); foreach ($immeubles as $immeuble) : ?>
				<a href="">
					<li class="immeuble">
						<strong><?php $carto->afficherImmeuble($immeuble); ?> <?php $carto->afficherRue($parcours['rue_id']); ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</section>