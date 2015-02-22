<?php
	
	// On regarde si on a bien les informations nécessaires
	if (isset($_POST['evenement']))
	{
		$event = new Evenement($_POST['evenement'], false);
		
		$query = $event->suppression();
	}
	else
	{
		return false;
	}
?>