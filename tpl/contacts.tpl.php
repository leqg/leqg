<section id="contacts">
	<section id="enteteTableau">
		<h2>Votre fichier consolidé</h2>
		
		<nav class="boutonsAction">
			<a href="<?php echo $core->tpl_go_to('fiche', array('operation' => 'creation')); ?>">Nouveau contact</a>
			<a href="<?php echo $core->tpl_go_to('fiche', array('operation' => 'fusion')); ?>">Fusion de fiches</a>
			<a href="#" class="ouvertureOverlay" data-overlay="ajoutCritere">Ajouter un critère</a>
		</nav>
		
		<div id="criteres" class="listeTags"><span class="tag" data-critere="coordonnees">Fiches avec coordonnées</span></div>
	</section>
	<section id="blocFiches">
		<table id="listeFiches">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>Nom</th>
					<th>Email</th>
					<th>Mobile</th>
					<th>Fixe</th>
					<th>Tags</th>
				</tr>
			</thead>
			
			<tbody>
				<?php
					$argsOnLoad = array('coordonnees' => true);
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
</section>

<div id="ajoutCritere" class="overlayForm">
	<form method="post" action="ajax.php?script=user-modification" id="formModificationCompte">
		<a class="fermetureOverlay" href="#">&#xe813;</a>
		<h3>Ajout d'un critère de tri</h3>
		<ul>
			<li>
				<label>Critère de tri</label>
				<div class="radio"><input type="radio" name="critere" id="critere-coordonnees" value="8" required><label for="critere-coordonnees"><span><span></span></span>Coordonnées (mail, téléphone)</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-etatcivil" value="5" required><label for="critere-etatcivil"><span><span></span></span>État civil</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-adresse" value="5" required><label for="critere-adresse"><span><span></span></span>Adresse</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-tags" value="1" required><label for="critere-tags"><span><span></span></span>Tags</label></div>
			</li>
			<li>
				<input type="submit" value="Définir le critère">
			</li>
		</ul>
		<ul id="detail-critere-coordonnees">
			<li>
				<label>Afficher les fiches possédant</label>
				<div class="radio"><input type="radio" name="critere" id="critere-coordonnees" value="8" required><label for="critere-coordonnees"><span><span></span></span>Coordonnées (mail, téléphone)</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-etatcivil" value="5" required><label for="critere-etatcivil"><span><span></span></span>État civil</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-adresse" value="5" required><label for="critere-adresse"><span><span></span></span>Adresse</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-tags" value="1" required><label for="critere-tags"><span><span></span></span>Tags</label></div>
			</li>
		</ul>
	</form>
</div>








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