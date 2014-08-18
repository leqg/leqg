<?php
	
	// On supprime la liaison entre la fiche et le dossier
	if ($dossier->supprimerLiaisonInteraction($_GET['interaction'])) {
		$core->tpl_go_to('fiche', array('id' => $_GET['id'] , 'interaction' => $_GET['interaction']), true);
	} else {
		$core->tpl_go_to('fiche', array('id' => $_GET['id']), true);
	}

?>