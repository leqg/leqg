<?php
	// On lance la connexion
	$link = Configuration::read('db.link');
	
	// On réalise l'inscription
	if (isset($_POST['mission'])) {
		$userId = User::ID();
		
		$query = $link->prepare('INSERT INTO `inscriptions` (`mission_id`, `user_id`) VALUES (:mission, :user)');
		$query->bindParam(':mission', $_POST['mission'], PDO::PARAM_INT);
		$query->bindParam('user', $userId, PDO::PARAM_INT);
		$query->execute();
	}
?>