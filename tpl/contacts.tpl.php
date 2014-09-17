<section id="contacts">
	<h2>Votre fichier consolidé</h2>
	
	<nav class="boutonsAction">
		<a href="<?php echo $core->tpl_go_to('fiche', array('operation' => 'creation')); ?>">Nouveau contact</a>
		<a href="<?php echo $core->tpl_go_to('fiche', array('operation' => 'fusion')); ?>">Fusion de fiches</a>
		<a href="#">Ajouter un critère</a>
	</nav>
	
	<div id="criteres"></div>
	
	<table id="listeFiches">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Nom</th>
				<th>Email</th>
				<th>Mobile</th>
				<th>Tél.</th>
				<th>Tags</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				$argsOnLoad = array('tag' => 'parti');
				$contacts = $fiche->liste('php', $argsOnLoad, 5000);
				foreach ($contacts as $contact) :
			?>
			<tr>
				<td><div class="radio"><input type="checkbox" name="fiche-<?php echo $contact['id']; ?>" id="fiche-<?php echo $contact['id']; ?>"><label for="fiche-<?php echo $contact['id']; ?>"><span><span></span></span></label></div></td>
				<td><a href="<?php $core->tpl_go_to('fiche', array('id' => $contact['id'])); ?>"><?php $fiche->affichageNomByID($contact['id']); ?></a></td>
				<td><?php $fiche->contact('email', false, false, $contact['id']); ?></td>
				<td><?php $core->tpl_phone($fiche->contact('mobile', false, true, $contact['id'])); ?></td>
				<td><?php $core->tpl_phone($fiche->contact('telephone', false, true, $contact['id'])); ?></td>
				<td class="listeTags"><?php $fiche->tags('span', false, $contact['id']); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</section>










<section id="contacts-ancien" style="display:none;">
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
				<strong><?php echo $t['description']; ?></strong>
				<?php if (!empty($t['historique_id']) && $t['historique_id'] > 0) : ?>
				<ul>
					<?php $interaction = $historique->recherche($t['historique_id']); ?>
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