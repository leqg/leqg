<?php

	// Script de recherche
	$recherche = $core->formatage_recherche($_POST['recherche']);
	
	// On effectue la recherche des fiches dont les tags correspondent
	$query = 'SELECT	* 
			  FROM		`contacts`
			  WHERE 	CONCAT_WS(" ", `contact_prenoms`, `contact_nom`, `contact_nom_usage`, `contact_nom`, `contact_prenoms`) LIKE "%' . $recherche . '%"
			  AND		`contact_id` != "' . $_POST['fiche1'] . '"
			  ORDER BY	`contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC
			  LIMIT		0, 30';
			  
	$sql = $db->query($query);
	
	if ($sql->num_rows > 0) :
		while ($contacts = $sql->fetch_assoc()) : $contact = $core->formatage_donnees($contacts);
?>
<?php if ($_GET['fiche'] == 1) { ?>
<a href="<?php $core->tpl_go_to('fiche', array('operation' => 'fusion', 'fiche1' => $contact['id'])); ?>">
<?php } else { ?>
<a href="<?php $core->tpl_go_to('fiche', array('operation' => 'fusion', 'fiche1' => $_POST['fiche1'], 'fiche2' => $contact['id'])); ?>">
<?php } ?>
	<li class="electeur">
		<strong><?php echo strtoupper($contact['nom']); ?> <?php echo strtoupper($contact['nom_usage']); ?> <?php echo ucwords(strtolower($contact['prenoms'])); ?></strong>
	</li>
</a>
<?php
		endwhile;
	else :
?>
	<li class="vide"><strong>Aucun résultat</strong></li>
<?php endif; ?>