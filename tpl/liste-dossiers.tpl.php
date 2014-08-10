<section id="liste-dossier" class="central">
	<h3>Dossiers ouverts actuellement</h3>
	<ul id="listeDossiers">
		<?php $dossiers = $dossier->recherche(); foreach ( $dossiers as $d ) : ?>
		<a href="<?php $core->tpl_get_url('dossier', $d['id']); ?>">
			<li id="dossier-<?php echo $d['id']; ?>" <?php if (!$d['statut']) { ?>class="dossierFerme"<?php } ?>>
				<strong><?php echo $d['nom']; ?></strong>
 				<?php if (strlen($d['description']) > 150) { ?>
 				<p><?php echo substr(stripslashes($d['description']), 0, 150); ?>&hellip;</p>
 				<?php } else { ?>
 				<p><?php echo stripslashes($d['description']); ?></p>
 				<?php } ?>
			</li>
		</a>
		<?php endforeach; ?>
	</ul>
</section>