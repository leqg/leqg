<?php
	// On récupère l'ID de l'interaction demandée
	$id = $_POST['id'];
	
	// On récupère les informations liées à l'interaction demandée
	$interaction = $historique->recherche( $id );
	
	// On prépare l'affichage de la date
	$date = strtotime($interaction['date']);
	
	// On prépare la liste des tags
	$tags = explode(',', $interaction['thematiques']);
	$liste_tags = '';
	foreach($tags as $tag) $liste_tags .= '<span class="tag">' . $tag . '</span>';
?>
<nav class="navigationFiches">
	<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'])); ?>">Retour à l'historique</a>
	<?php if ($interaction['type'] != 'sms' && $interaction['type'] != 'courriel') : ?><a class="modifier" href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'])); ?>&modifier=true">Modifier</a><?php endif; ?>
</nav>

<h6>Interaction n° <?php echo $interaction['id']; ?></h6>

<ul class="deuxColonnes petit">
	<li><!--
	 --><span class="label-information">Type</span><!--
	 --><p><?php $historique->returnType($interaction['type']); ?></p><!--
 --></li>
	<li><!--
	 --><span class="label-information">Date</span><!--
	 --><p><?php echo strtolower(htmlentities(strftime('%A %e %B %Y', $date))); ?></p><!--
 --></li>
	<li>
		<span class="label-information">Lieu</span>
	 	<p>
	 	<?php if (!empty($interaction['lieu'])) : ?>
	 		<?php echo ucwords($interaction['lieu']); ?>
	 	<?php else : ?>
	 		&nbsp;
	 	<?php endif; ?>
	 	</p>
	 </li>
	<li><!--
	 --><span class="label-information">Objet</span><!--
	 --><p><?php echo $interaction['objet']; ?></p><!--
 --></li>
	<li>
		<span class="label-information">Notes</span>
	 	<?php if ($interaction['type'] == 'courriel') : ?>
	 	<p><?php echo nl2br(html_entity_decode($interaction['notes'], ENT_HTML5, 'UTF-8')); ?></p>
	 	<?php elseif (!empty($interaction['notes'])) : ?>
	 	<p><?php echo nl2br($interaction['notes']); ?></p>
	 	<?php else : ?>
	 	<p>&nbsp;</p>
	 	<?php endif; ?>
	</li>
	<li>
		<span class="label-information">Tâches</span>
		<ul class="listeEncadree">
			<?php
				$taches = $tache->listeParInteraction($interaction['id']);
				
				foreach ($taches as $task) :
			?>
			<a href="ajax.php?script=fin-tache&tache=<?php echo $task['id']; ?>&fiche=<?php echo $interaction['contact_id']; ?>&interaction=<?php echo $interaction['id']; ?>">
				<li class="tache">
					<strong><?php echo $task['description']; ?></strong>
					<?php if (!is_null($task['compte_id'])) : ?><p>Attribué à <?php echo $user->get_login_by_ID($task['compte_id']); ?></p><?php endif; ?>
				</li>
			</a>
			<?php endforeach; ?>
			<a href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'], 'ajoutTache' => 'true')); ?>">
				<li class="tache ajoutTache">
					<strong>Ajouter une nouvelle tâche</strong>
				</li>
			</a>
		</ul>
	</li> 
 	<li><!--
	 --><span class="label-information">Dossier</span><!--
	 --><ul class="listeEncadree">
 		<?php $d = $historique->dossier($interaction['id']); if ($d) : ?>
	 		<a href="<?php $core->tpl_go_to('dossier', array('id' => $d['id'])); ?>"><li class="dossier <?php if ($d['statut'] == 0) { echo 'ferme'; } ?>"><strong>Dossier <?php $core->sortie($d['nom']); ?></strong><p><?php $core->sortie($d['description']); ?></p></li></a>
 		<?php else : ?>
	 		<a href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'], 'dossier' => 'true')); ?>"><li class="dossier ajoutDossier"><strong>Ajouter à un dossier</strong></li></a>
 		<?php endif; ?>
		</ul><!--
 --></li>
	<li><!--
	 --><span class="label-information">Fichiers</span><!--
	 --><ul class="listeEncadree">
			<?php if ($fichier->nombreFichiers('interaction' , $interaction['id'])) : ?>
			<?php $fichiers = $fichier->listeFichiers('interaction' , $interaction['id']); foreach ($fichiers as $f) : ?>
			<?php if(!empty($f['url'])) { ?><a href="uploads/<?php echo $f['url']; ?>" target="_blank"><?php } ?>
				<li class="fichier <?php echo $fichier->extension($f['id']); ?>">
					<strong><?php echo $f['nom']; ?></strong>
					<?php if (!empty($f['reference']) || !empty($f['description'])) : ?>
					<p>
						<?php if (!empty($f['reference']) && empty($f['description'])) echo 'Référence <em>' . $f['reference'] . '</em>'; ?>
						<?php if (empty($f['reference']) && !empty($f['description'])) echo $f['description']; ?>
						<?php if (!empty($f['reference']) && !empty($f['description'])) echo 'Référence <em>' . $f['reference'] . '</em><br>' . $f['description']; ?>
					</p>
					<?php endif; ?>
				</li>
			<?php if(!empty($f['url'])) { ?></a><?php } ?>
			<?php endforeach; endif; ?>
			<a href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'], 'fichier' => 'true')); ?>"><li class="fichier ajoutFichier"><strong>Ajouter un nouveau fichier</strong></li></a>
		</ul><!--
 --></li>
</ul>