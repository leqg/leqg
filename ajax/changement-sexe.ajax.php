<?php
	// On récupère le numéro de la fiche à modifier
	$id = $_POST['fiche'];
	
	// On récupère l'information actuelle du sexe
	$query = 'SELECT contact_sexe FROM contacts WHERE contact_id = ' . $id;
	$sql = $db->query($query);
	$row = $sql->fetch_assoc();
	
	// On transforme le sexe
	if ($row['contact_sexe'] == 'M') { $sexe = 'F'; } else { $sexe = 'M'; }
	
	// On enregistre le nouveau sexe dans la base de données
	$query = 'UPDATE contacts SET contact_sexe = "' . $sexe . '" WHERE contact_id = ' . $id;
	$db->query($query);
?>
Bon ça y est ! :)