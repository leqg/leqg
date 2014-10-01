<section id="contacts">
	<section id="enteteTableau">
		<h2>Votre fichier consolidé</h2>
		
		<nav class="boutonsAction">
			<a href="<?php echo $core->tpl_go_to('fiche', array('operation' => 'creation')); ?>">Nouveau contact</a>
			<a href="<?php echo $core->tpl_go_to('fiche', array('operation' => 'fusion')); ?>">Fusion de fiches</a>
			<a href="#" class="ouvertureOverlay" data-overlay="ajoutCritere">Ajouter un critère</a>
		</nav>
		
		<div id="criteres" class="listeTags"><span class="tag" data-critere="contact:tous">contact:tous</span></div>
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
			
			<tbody id="majListeFiches">
				<?php
					$argsOnLoad = array('contact' => 'tous');
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
	<form method="post" action="ajax.php?script=contacts-liste" id="form-ajoutCritere">
		<input type="hidden" name="summaryTri" id="summaryTri" value="contact:tous">
		<a class="fermetureOverlay" data-overlay="ajoutCritere" href="#">&#xe813;</a>
		<h3>Ajout d'un critère de tri</h3>
		<ul>
			<li>
				<label>Critère de tri</label>
				<div class="radio"><input type="radio" name="critere" id="critere-contact" class="selectionCritere" data-critere="contact" value="contact" required><label for="critere-contact"><span><span></span></span>Coordonnées (mail, téléphone)</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-electoral" class="selectionCritere" data-critere="electoral" value="electoral" required><label for="critere-electoral"><span><span></span></span>Liste électorale</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-bureau" class="selectionCritere" data-critere="bureau" value="bureau" required><label for="critere-bureau"><span><span></span></span>Bureau de vote</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-etatcivil" class="selectionCritere" data-critere="etatcivil" value="etatcivil" required><label for="critere-etatcivil"><span><span></span></span>État civil</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-adresse" class="selectionCritere" data-critere="adresse" value="adresse" required><label for="critere-adresse"><span><span></span></span>Adresse</label></div>
				<div class="radio"><input type="radio" name="critere" id="critere-tags" class="selectionCritere" data-critere="tags" value="tags" required><label for="critere-tags"><span><span></span></span>Tags</label></div>
			</li>
			<li class="detail-critere detail-critere-contact affichageOptionnel">
				<label>Afficher les fiches où</label>
				<div class="radio"><input type="radio" name="contact" id="contact-tous" value="tous"><label for="contact-tous"><span><span></span></span>Un téléphone ou l'email est connu</label></div>
				<div class="radio"><input type="radio" name="contact" id="contact-email" value="email"><label for="contact-email"><span><span></span></span>L'adresse email est connue</label></div>
				<div class="radio"><input type="radio" name="contact" id="contact-mobile" value="mobile"><label for="contact-mobile"><span><span></span></span>Le mobile est connu</label></div>
				<div class="radio"><input type="radio" name="contact" id="contact-telephone" value="telephone"><label for="contact-telephone"><span><span></span></span>Le fixe est connu</label></div>
			</li>
			<li class="detail-critere detail-critere-electoral affichageOptionnel">
				<label>Afficher les fiches où</label>
				<div class="radio"><input type="radio" name="electoral" id="electoral-oui" value="oui"><label for="electoral-oui"><span><span></span></span>Le contact est électeur</label></div>
				<div class="radio"><input type="radio" name="electoral" id="electoral-non" value="non"><label for="electoral-non"><span><span></span></span>Le contact n'est pas électeur</label></div>
			</li>
			<li class="detail-critere detail-critere-bureau affichageOptionnel">
				<label>Les électeurs au sein du bureau de vote</label>
				<select name="bureau" id="listeBureau">
					<?php $bureaux = $carto->listeTousBureaux(); foreach ($bureaux as $bureau) : ?>
					<option value="<?php echo $bureau['id']; ?>" data-numero="<?php echo $bureau['numero']; ?>">Bureau <?php echo $bureau['numero']; ?> – <?php $carto->afficherVille($bureau['commune_id']); ?></option>
					<?php endforeach; ?>
				</select>
			</li>
			<li class="detail-critere detail-critere-tags affichageOptionnel">
				<label>Afficher les fiches avec pour tag</label>
				<input type="text" name="tags" id="tagDemande">
			</li>
			<li>
				<input type="submit" value="Ajouter le critère de tri">
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