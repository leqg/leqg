<?php
	// On créé le lien vers la BDD
	$dsn =  'mysql:host=' . Configuration::read('db.host') . 
			';dbname=' . Configuration::read('db.basename');
	$user = Configuration::read('db.user');
	$pass = Configuration::read('db.pass');
	
	$link = new PDO($dsn, $user, $pass);


	if (isset($_POST['id']))
	{
		// On recherche des informations sur le type de coordonnées et le contact concerné
		$query = $link->prepare('SELECT `contact_id`, `coordonnee_type` FROM `coordonnees` WHERE `coordonnee_id` = :id');
		$query->bindParam(':id', $_POST['id']);
		$query->execute();
		$infos = $query->fetch(PDO::FETCH_NUM);
		
		// On supprime maintenant la coordonnée
		$query = $link->prepare('DELETE FROM `coordonnees` WHERE `coordonnee_id` = :id');
		$query->bindParam(':id', $_POST['id']);
		$query->execute();
		
		// On retire une coordonnée dans l'enregistrement du contact
		$query = $link->prepare('UPDATE `contacts` SET `contact_' . $infos[1] . '` = `contact_' . $infos[1] . '` - 1 WHERE `contact_id` = :id');
		$query->bindParam(':id', $infos[0]);
		$query->execute();
	}
?>