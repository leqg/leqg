<section id="contacts">
	<div id="historique">
		<h3>Dernières interactions</h3>
		
		<ul class="timeline debutNow">
			<?php $interactions = $historique->dernieresInteractions(15); if (count($interactions) > 0) : foreach ($interactions as $interaction) : ?>
			<li class="<?php echo $interaction['type']; ?>">
				<strong><?php echo $interaction['objet']; ?></strong>
				<ul>
					<li class="contact"><a href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'])); ?>"><?php $fiche->affichageNomByID($interaction['contact_id']); ?></a></li>
					<li class="date"><?php echo date('d/m/Y', strtotime($interaction['date'])); ?></li>
					<li class="lieu"><?php echo $interaction['lieu']; ?></li>
				</ul>
			</li>
			<?php endforeach; endif; ?>
			<li class="fin"></li>
		</ul>
	</div><!--
	
 --><div id="taches">
		<h3>Dernières tâches créées</h3>
		
		<ul class="timeline debutNow">
			<?php $taches = $tache->liste(15); if (count($taches) > 0) : foreach ($taches as $t) : ?>
			<li class="tache">
				<?php $interaction = $historique->recherche($t['historique_id']); ?>
				<strong><a href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'])); ?>" class="nostyle"><?php if (empty($t['description'])) { $historique->returnType($t['type']); } else { echo $t['description']; } ?></a></strong>
				<?php if (!empty($t['historique_id']) && $t['historique_id'] > 0) : ?>
				<ul>
					<li class="contact"><a href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'])); ?>"><?php $fiche->affichageNomByID($interaction['contact_id']); ?></a></li>
					<li class="date"><?php echo date('d/m/Y', strtotime($t['creation'])); ?></li>
				</ul>
				<?php endif; ?>
			</li>
			<?php endforeach; endif; ?>
			<li class="fin"></li>
		</ul>
	</div>
</section>
