<?php

	// On récupère l'identifiant de la fiche à supprimer
	$id = $_GET['id'];
	
	// S'il s'agit bien d'une fiche, on lance la méthode de suppression des fiches
	if (is_numeric($id)) $user->suppression($id);
	
	// On retourne vers la liste des comptes
	$core->tpl_go_to('administration', true);

?>