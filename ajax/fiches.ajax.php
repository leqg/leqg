<?php
	
	if (isset($_GET['recherche'])) :
	
		$recherche = $core->formatage_recherche($_GET['recherche']);
		if (isset($_GET['limite'])) { $limite = $_GET['limite']; } else { $limite = null; }
	
		// On prépare le tableau de rendu
		$contacts = array();
	
		// On récupère les fiches dans la base de données
		if (is_null($limite))
		{
			$sql = $db->query('SELECT `contact_id`, `contact_nom`, `contact_nom_usage`, `contact_prenoms` FROM `contacts` WHERE `contact_id` != "' . $_GET['fiche'] . '" AND CONCAT_WS(" ", `contact_prenoms`, `contact_nom`, `contact_nom_usage`, `contact_nom`, `contact_prenoms`) LIKE "%' . $recherche . '%" ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC');
		}
		else
		{
			$sql = $db->query('SELECT `contact_id`, `contact_nom`, `contact_nom_usage`, `contact_prenoms` FROM `contacts` WHERE `contact_id` != "' . $_GET['fiche'] . '" AND CONCAT_WS(" ", `contact_prenoms`, `contact_nom`, `contact_nom_usage`, `contact_nom`, `contact_prenoms`) LIKE "%' . $recherche . '%" ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC LIMIT 0, ' . $limite);
		}
		while ($row = $sql->fetch_assoc()) $contacts[] = $row;
		
		// On retourne le tableau en JSON
		$retour = json_encode($contacts);
		echo $retour;
		
		
	
	endif;

?>