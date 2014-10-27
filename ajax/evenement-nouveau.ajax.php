<?php
	// On regarde si on a bien les informations nécessaires
	if (isset($_GET['contact']))
	{
		$event = new evenement($_GET['contact'], false, true);
		
		echo $event->json_infos();
	}
	else
	{
		return false;
	}
?>