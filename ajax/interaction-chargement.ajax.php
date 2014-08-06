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
	 --><?php $historique->returnType($interaction['type'], false); ?><!--
 --></li>
	<li><!--
	 --><label class="noform">Date</label><!--
	 --><?php echo strtolower(htmlentities(strftime('%A %e %B %Y', $date)));  ?><!--
 --></li>
	<li><!--
	 --><label class="noform">Lieu</label><!--
	 --><?php echo ucwords($interaction['lieu']); ?><!--
 --></li>
	<li class="liste-tags"><!--
	 --><label class="noform">Thématiques</label><!--
	 --><?php echo $liste_tags; ?><!--
 --></li>
	<li class="notes"><!--
	 --><label class="noform">Notes</label><!--
	 --><p><?php echo $interaction['notes']; ?></p><!--
 --></li>
</ul>
