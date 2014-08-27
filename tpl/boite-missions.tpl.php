<section id="fiche">
	<header class="boite">
		<h2>
			<span>Boîtage</span>
			<span>Liste des missions</span>
		</h2>
	</header>
	
	<ul class="listeEncadree">
		<?php $missions = $mission->liste('boite'); foreach ($missions as $parcours) : ?>
		<a href="<?php $core->tpl_go_to('boite', array('action' => 'mission', 'mission' => $parcours['id'])); ?>">
			<li class="rue">
				<strong><?php $carto->afficherRue($parcours['rue_id']); ?>, <?php $carto->afficherVille($parcours['ville_id']); ?></strong>
			</li>
		</a>
		<?php endforeach; ?>
	</ul>
</section>