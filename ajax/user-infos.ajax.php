<?php
	header('Content-Type: application/json');

	// On récupère l'ID du compte dont nous devons chercher les informations
	if (is_numeric($_GET['user'])) : $id = $_GET['user']; else : $id = null; endif;
	
	if (!is_null($id)) :
	
		$infos = $user->infos_publiques($id);
		
		echo json_encode($infos);
	
	else :
	
		
	
	endif;

?>