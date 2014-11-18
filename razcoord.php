<?php
	require_once 'includes.php';
	
	// On charge chaque coordonnée enregistrée dans notre base de données
	$query = $link->query('SELECT * FROM `coordonnees`');
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $coord) {
		// On incrémente le nombre d'entrée des coordonnées selon le type de coordonnées
		$sql = $link->prepare('UPDATE `contacts` SET `contact_' . $coord['coordonnee_type'] . '` = `contact_' . $coord['coordonnee_type'] . '` + 1 WHERE `contact_id` = :id');
		$sql->bindParam(':id', $coord['contact_id'], PDO::PARAM_INT);
		$sql->execute();
		unset($sql);
	}