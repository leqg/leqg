<?php

	// On récupère les informations
	$infos = $_POST;
	
	// On retraite le nom de la rue pour le protéger dans la base
	$infos['nom'] = $core->securisation_string($infos['nom']);
	$infos['rue'] = $core->securisation_string($infos['rue']);
	
	// On enregistre les informations dans la base de données
	$query = 'UPDATE `rues` SET `rue_nom` = "' . $infos['nom'] . '" WHERE `rue_id` = ' . $infos['rue'];
	$db->query($query);
	
	// On redirige vers la page de la rue
	$core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'rue', 'rue' => $infos['rue']), true);

?>