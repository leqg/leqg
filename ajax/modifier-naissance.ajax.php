<?php

	// On va voir si la ville a bien été entrée
	if (!empty($_POST['ville'])) :
		$ville = $_POST['ville'];
		$query = 'UPDATE		contacts
				  SET		contact_naissance_commune_id = ' . $ville . '
				  WHERE		contact_id = ' . $_GET['fiche'];
		$db->query($query);
	endif;
	
	// On va voir si la date a bien été entrée
	if (!empty($_POST['dateNaissance'])) :
		$date = explode('/', $_POST['dateNaissance']);
		krsort($date);
		$date = implode('-', $date);
		$query = 'UPDATE		contacts
				  SET		contact_naissance_date = "' . $date . '"
				  WHERE		contact_id = ' . $_GET['fiche'];
		$db->query($query);
	endif;
	
	// On enregistre les nouvelles informations dans la base de données
	$core->tpl_go_to('fiche', array('id' => $_GET['fiche']), true);

?>