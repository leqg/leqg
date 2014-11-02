<?php $core->tpl_header(); ?>
	
	<h2>Dossiers en cours</h2>
	
	<?php
		// On récupère la liste des dossiers ouverts
		$dossiers = Folder::liste();
		
		if (count($dossiers) > 0) :
	?>
	<section id="dossiers">
		<ul class="liste-dossiers">
		<?php
			// On fait une boucle des dossiers ouverts
			foreach ($dossiers as $dossier) :
			
			// On ouvre l'objet contenant le dossier
			$d = new Folder(md5($dossier['dossier_id']));
		?>
			<li>
				<a href="<?php $core->tpl_go_to('dossier', array('dossier' => md5($d->get('dossier_id')))); ?>" class="nostyle"><h4><?php echo $d->get('dossier_nom'); ?></h4></a>
				<p><?php echo $d->get('dossier_description'); ?></p>
			</li>
		<?php endforeach; ?>
		</ul>
	</section>
	<?php else: ?>
	<section class="icone" id="aucunDossier">
		<h3>Aucun dossier ouvert actuellement.</h3>
	</section>
	<?php endif; ?>
	
<?php $core->tpl_footer(); ?>