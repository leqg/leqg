<div id="historique">
	<nav class="navigationFiches">
		<a class="liste" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_ID(), 'afficherDossiers' => 'true')); ?>">Dossiers</a>
	</nav>
	
	<h6>Historique des interactions</h6>
	<?php $interactions = $historique->rechercheParFiche($fiche->get_the_ID()); // On initialise la liste des interactions à afficher ?>
	
	<table id="historique-contact">
		<thead>
			<tr>
				<th>Type</th>
				<th>Date</th>
				<th>Objet <a class="add-historique" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id(), 'nouvelleInteraction' => 'true')); ?>">&#xe816;</span></th>
				<!--<th>Thématiques</th>-->
			</tr>
		</thead>
		<tbody>
			<?php foreach ($interactions as $interaction) : ?>
			<tr>
				<td><?php $historique->returnType($interaction['type']); ?></td>
				<td><?php echo date('d/m/Y', strtotime($interaction['date'])); ?></td>
				<td>
				<?php if ($interaction['type'] == 'sms') : ?>
					<?php echo $interaction['notes']; ?>
				<?php else : ?>
					<a href="<?php $core->tpl_get_url('fiche', $fiche->get_the_ID(), 'id', $interaction['id'], 'interaction'); ?>"><?php echo $interaction['objet']; ?></a>
				<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>