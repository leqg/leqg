<section id="fiche">
	<header class="porte">
		<h2>
			<span>Porte-à-porte</span>
			<span>Liste des missions</span>
		</h2>
	</header>
	
	<ul class="listeEncadree">
		<?php $missions = $mission->liste('porte'); foreach ($missions as $parcours) : ?>
		<a href="<?php $core->tpl_go_to('porte', array('action' => 'mission', 'mission' => $parcours['id'])); ?>">
			<li class="rue">
				<strong><?php $carto->afficherRue($parcours['rue_id']); ?>, <?php $carto->afficherVille($parcours['ville_id']); ?></strong>
			</li>
		</a>
		<?php endforeach; ?>
	</ul>
</section>