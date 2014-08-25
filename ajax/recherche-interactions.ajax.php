<?php

	// Script de recherche
	$recherche = $core->formatage_recherche($_POST['recherche']);
	
	// On effectue la recherche des fiches dont les tags correspondent
	$query = 'SELECT	*
			  FROM		historique
			  WHERE		( historique_objet LIKE "%' . $recherche . '%" OR historique_thematiques LIKE "%' . $recherche . '%" )
			  AND		( historique_type != "sms" OR historique_type != "courriel" ) 
			  ORDER BY	historique_objet ASC';
			  
	$sql = $db->query($query);
	
	if ($sql->num_rows > 0) :
		while ($interactions = $sql->fetch_assoc()) : $interaction = $core->formatage_donnees($interactions);
?>
<a href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'])); ?>">
	<li class="interaction">
		<strong><?php echo $interaction['objet']; ?></strong>
		<p><?php echo $historique->returnType($interaction['type']); ?>&nbsp;&nbsp;–&nbsp;&nbsp;<?php echo $fiche->nomByID($interaction['contact_id']); ?></p>
	</li>
</a>
<?php
		endwhile;
	else :
?>
	<li class="vide"><strong>Aucun résultat</strong></li>
<?php endif; ?>