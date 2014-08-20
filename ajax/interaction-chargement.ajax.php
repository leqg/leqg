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
	<a class="modifier" href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'])); ?>&modifier=true">Modifier</a>
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
	<li><!--
	 --><span class="label-information">Lieu</span><!--
	 --><p><?php echo ucwords($interaction['lieu']); ?></p><!--
 --></li>
	<li><!--
	 --><span class="label-information">Objet</span><!--
	 --><p><?php echo $interaction['objet']; ?></p><!--
 --></li>
	<li><!--
	 --><span class="label-information">Notes</span><!--
	 --><p><?php echo nl2br($interaction['notes']); ?></p><!--
 --></li>
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