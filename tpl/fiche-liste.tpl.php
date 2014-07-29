<a href="<?php $core->tpl_get_url('fiche', $fiche->get_infos('id')); ?>">
	<article class="fiche">
		<header>
			<h3><?php $fiche->affichage_nom('span'); ?></h3>
		</header>
		<ul>
			<li><?php $fiche->date_naissance(' / '); ?> – <?php $fiche->age(); ?></li>
			<li><?php echo $core->tpl_transform_texte($fiche->get_infos('adresse_ville')); ?></li>
		</ul>
	</article>
</a>