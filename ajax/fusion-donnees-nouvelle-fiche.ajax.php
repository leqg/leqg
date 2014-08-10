<?php

	// On récupère d'abord les informations
		$contact = $_POST['contact'];
		$fixe = $_POST['fixe'];
		$mobile = $_POST['mobile'];
		$email = $_POST['email'];
	
	// Pour chaque champ remplis, on le met à jour dans la base de données
		if ($fixe != '') {
			$fixe = preg_replace('`[^0-9]`', '', $fixe);
			
			$query = 'UPDATE contacts SET contact_telephone = "' . $fixe . '" WHERE contact_id = ' . $contact;
			$db->query($query);
		}

		if ($mobile != '') {
			$mobile = preg_replace('`[^0-9]`', '', $mobile);
			
			$query = 'UPDATE contacts SET contact_mobile = "' . $mobile . '" WHERE contact_id = ' . $contact;
			$db->query($query);
		}

		if ($email != '') {
			$query = 'UPDATE contacts SET contact_email = "' . $email . '" WHERE contact_id = ' . $contact;
			$db->query($query);
		}
?>
Réussite