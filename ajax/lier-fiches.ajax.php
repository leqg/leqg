<?php

	// On récupère les informations
	$infos = $_POST;
	
	// On exécute la requête
	$db->query('INSERT INTO `liaisons` (`ficheA`, `ficheB`) VALUES (' . $infos['ficheA'] . ', ' . $infos['ficheB'] . ')');

?>