<a href="<?php $core->tpl_get_url('fiche', $fiche->get_infos('id')); ?>">
	<article class="fiche">
		<header>
			<h3><?php $fiche->affichage_nom('span'); ?></h3>
		</header>
		<ul>
			<li><?php $fiche->date_naissance(' / '); ?> – <?php $fiche->age(); ?></li>
			<li><?php $carto->afficherVille($carto->villeParImmeuble($fiche->get_immeuble())); ?></li>
		</ul>
	</article>
</a>