<?php
	// On créé le lien vers la BDD
	$dsn =  'mysql:host=' . Configuration::read('db.host') . 
			';dbname=' . Configuration::read('db.basename');
	$user = Configuration::read('db.user');
	$pass = Configuration::read('db.pass');
	
	$link = new PDO($dsn, $user, $pass);


	if (isset($_POST['id']))
	{
		$query = $link->prepare('DELETE FROM `coordonnees` WHERE `coordonnee_id` = :id');
		$query->bindParam(':id', $_POST['id']);
		
		// On exécute la modification
		$query->execute();
	}
?>