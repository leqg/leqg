<?php

// On récupère le contenu du fichier comme requête SQL
$sql = file_get_contents('squelette.sql');

// On extrait toutes les requêtes SQL
$req = explode(';', $sql);

// On exécute toutes les requêtes
foreach ($req as $r) {
	$db->query($r);
}

?>
Fini!