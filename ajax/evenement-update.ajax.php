<?php
	
	// On regarde si on a bien les informations nécessaires
	if (isset($_POST['evenement'], $_POST['info'], $_POST['value']))
	{
		$event = new evenement($_POST['evenement'], false);
		
		$event->modification($_POST['info'], $_POST['value']);
	}
	else
	{
		return false;
	}
?>