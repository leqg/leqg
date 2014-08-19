<?php

	// On récupère les informations envoyées
	$infos = array('fiche' => $_POST['fiche'],
				   'ville' => $_POST['ville'],
				   'rue' => $_POST['rue'],
				   'numero' => $_POST['immeuble']);
				   
	// On ajout l'immeuble dans la BDD
	$id = $carto->ajoutImmeuble($infos);
	
	// On lie le contact à l'immeuble
	$fiche->modificationAdresse($infos['fiche'], $id);
	
	$core->tpl_go_to('fiche', array('id' => $infos['fiche']), true);
?>