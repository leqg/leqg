<?php

	// On commence par charger les includes nécessaires au système
	require_once 'includes.php';
	
	// On récupère le hash demandé
	$hash = $_GET['email'];
	
	// On regarde dans la base de données si un compte correspond
	$query = 'SELECT * FROM `users` WHERE `user_new_email_hash` = "' . $hash . '"';
	$sql = $noyau->query($query);
	
	if ($sql->num_rows == 1) {
		// On modifie dans la base de données l'email demandé et en profite pour réinitialiser les connexions au compte en question
		$query = 'UPDATE `users` SET `user_reinit` = NOW(), `user_email` = `user_new_email`, `user_new_email` = NULL, `user_new_email_hash` = NULL WHERE `user_new_email_hash` = "' . $hash . '"';
		$core->tpl_go_to(true);
		
		if ($noyau->query($query)) {
			
		} else {
			$core->tpl_go_to(true);
		}
	} else {
		$core->tpl_go_to(true);
	}

?>