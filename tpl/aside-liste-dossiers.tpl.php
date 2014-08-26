<div id="listeDesDossiers">
	<nav class="navigationFiches">
		<a class="liste" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_ID())); ?>">Historique</a>
	</nav>
	
	<h6>Dossiers liés à la fiche contact</h6>

	<ul class="listeEncadree">
		<?php $dossiers = $dossier->rechercheParFiche($fiche->get_the_ID()); foreach ($dossiers as $d) : ?>
		<a href="<?php $core->tpl_go_to('dossier', array('id' => $d['id'])); ?>">
			<li class="dossier">
				<strong><?php echo $d['nom']; ?></strong>
				<?php if (!empty($d['description'])) { ?><p><?php echo $d['description']; ?></p><?php } ?>
			</li>
		</a>
		<?php endforeach; ?>
	</ul>
</div>