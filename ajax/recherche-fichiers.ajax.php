<?php

	// Script de recherche
	$recherche = $core->formatage_recherche($_POST['recherche']);
	
	// On effectue la recherche des fiches dont les tags correspondent
	$query = 'SELECT	*
			  FROM		fichiers
			  WHERE		fichier_nom LIKE "%' . $recherche . '%"
			  OR		fichier_labels LIKE "%' . $recherche . '%"
			  OR		fichier_description LIKE "%' . $recherche . '%"
			  ORDER BY	fichier_nom ASC';
			  
	$sql = $db->query($query);
	
	if ($sql->num_rows > 0) :
		while ($fichiers = $sql->fetch_assoc()) : $file = $core->formatage_donnees($fichiers); $interaction = $historique->recherche($file['interaction_id']);
?>
<a href="<?php $core->tpl_go_to('fiche', array('id' => $file['contact_id'], 'interaction' => $file['interaction_id'])); ?>">
	<li class="fichier">
		<strong><?php echo $file['nom']; ?></strong>
		<p><?php echo $fiche->nomByID($file['contact_id']); ?>&nbsp;&nbsp;–&nbsp;&nbsp;<?php echo $file['description']; ?></p>
	</li>
</a>
<?php
		endwhile;
	else :
?>
	<li class="vide"><strong>Aucun résultat</strong></li>
<?php endif; ?>