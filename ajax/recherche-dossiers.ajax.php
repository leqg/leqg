<?php

	// Script de recherche
	$recherche = $core->formatage_recherche($_POST['recherche']);
	
	// On effectue la recherche des fiches dont les tags correspondent
	$query = 'SELECT	*
			  FROM		dossiers
			  WHERE		dossier_nom LIKE "%' . $recherche . '%"
			  OR			dossier_description LIKE "%' . $recherche . '%"
			  ORDER BY	dossier_nom ASC';
			  
	$sql = $db->query($query);
	
	if ($sql->num_rows > 0) :
		while ($dossiers = $sql->fetch_assoc()) : $doss = $core->formatage_donnees($dossiers);
?>
<a href="<?php $core->tpl_go_to('dossier', array('id' => $doss['id'])); ?>">
	<li class="dossier">
		<strong><?php echo $doss['nom']; ?></strong>
	</li>
</a>
<?php
		endwhile;
	else :
?>
	<li class="vide"><strong>Aucun résultat</strong></li>
<?php endif; ?>