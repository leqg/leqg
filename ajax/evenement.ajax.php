<?php

if (is_string($_GET['evenement']))
{
	// On ouvre l'événement
	$event = new Evenement($_GET['evenement']);
	
	// On récupère les informations et on retourne le JSON
	echo $event->json_infos();
}	
else
{
	return false;
}
	
?>