<a href="<?php $core->tpl_go_to('contact', array('contact' => md5($fiche->get_infos('id'))), true); ?>">
	<article class="fiche">
		<header>
			<h3><?php $fiche->affichage_nom('span'); ?></h3>
		</header>
		<ul>
			<li><?php if ($fiche->get_infos('naissance_date') != '0000-00-00') : $fiche->date_naissance(' / '); ?> – <?php $fiche->age(); endif; ?></li>
			<?php if ($fiche->is_adresse_fichier()) : ?><li><?php $carto->afficherVille($carto->villeParImmeuble($fiche->get_immeuble())); ?></li><?php endif; ?>
		</ul>
	</article>
</a>