<?php

	// Script de recherche
	$recherche = $core->formatage_recherche($_POST['recherche']);
	
	// On effectue la recherche des fiches dont les tags correspondent
	$query = 'SELECT	*
			  FROM		contacts
			  WHERE		contact_tags LIKE "%' . $recherche . '%"
			  ORDER BY	contact_nom, contact_nom_usage, contact_prenoms ASC LIMIT 0,30';
			  
	$sql = $db->query($query);
	
	if ($sql->num_rows > 0) :
		while ($contacts = $sql->fetch_assoc()) : $contact = $core->formatage_donnees($contacts);
?>
<a href="<?php $core->tpl_go_to('contact', array('id' => $contact['id'])); ?>">
	<li class="electeur">
		<strong><?php echo $contact['nom']; ?></strong>
	</li>
</a>
<?php
		endwhile;
	else :
?>
	<li class="vide"><strong>Aucun résultat</strong></li>
<?php endif; ?>