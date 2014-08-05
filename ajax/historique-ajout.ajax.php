<?php

	// On recueille les données
	$donnees['contact'] = $_POST['fiche'];
	$donnees['compte'] = $_COOKIE['leqg-user'];
	$donnees['type'] = $_POST['type'];
	$donnees['date'] = $_POST['date'];
	$donnees['lieu'] = $_POST['lieu'];
	$donnees['thematiques'] = $_POST['themas'];
	$donnees['notes'] = $_POST['notes'];

	// On lance l'enregistrement des données dans la base de données
	$enregistrement = $historique->ajout( $donnees['contact'] , $donnees['compte'] , $donnees['type'] , $donnees['date'] , $donnees['lieu'] , $donnees['thematiques'] , $donnees['notes'] );
	
	// On affiche en valeur de retour à la fonction AJAX le numéro ID d'enregistrement
	echo $enregistrement;
?>