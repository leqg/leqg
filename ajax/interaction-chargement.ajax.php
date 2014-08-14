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
<h6>Interaction n° <?php echo $interaction['id']; ?></h6>

<ul class="ficheInteraction">
	<li><!--
	 --><label>Type</label><!--
	 --><p><?php $historique->returnType($interaction['type']); ?></p><!--
 --></li>
	<li><!--
	 --><label class="noform">Date</label><!--
	 --><p><?php echo strtolower(htmlentities(strftime('%A %e %B %Y', $date))); ?></p><!--
 --></li>
	<li><!--
	 --><label class="noform">Lieu</label><!--
	 --><p><?php echo ucwords($interaction['lieu']); ?></p><!--
 --></li>
	<li><!--
	 --><label class="noform">Objet</label><!--
	 --><p><?php echo $interaction['objet']; ?></p><!--
 --></li>
	<li class="notes"><!--
	 --><label class="noform">Notes</label><!--
	 --><p><?php echo nl2br($interaction['notes']); ?></p><!--
 --></li>
	<?php if ($fichier->nombreFichiers('interaction' , $interaction['id'])) : ?>
	<li><!--
	 --><label class="noform">Fichiers</label><!--
	 --><ul class="listeFichiers">
			<?php $fichiers = $fichier->listeFichiers('interaction' , $interaction['id']); foreach ($fichiers as $f) : ?>
			<li><a href="uploads/<?php echo $f['url']; ?>" target="_blank"><?php echo $f['nom']; ?></a></li>
			<?php endforeach; ?>
		</ul><!--
 --></li>
 	<?php endif; ?>
</ul>

<ul class="listeActions">
	<li><a href="<?php $core->tpl_get_url('fiche', $interaction['contact_id'], 'id', $interaction['id'], 'objet'); ?>&fichier=true">Ajouter un fichier</a></li>
	<li>Ajouter à un dossier</li>
	<li><a href="<?php $core->tpl_get_url('fiche', $interaction['contact_id']); ?>">Retour à l'historique</a></li>
</ul>