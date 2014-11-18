<?php
	
	if (isset($_GET['recherche'], $_GET['fiche'])) :
	
		$recherche = Core::searchFormat($_GET['recherche']);
		if (isset($_GET['limite'])) { $limite = $_GET['limite']; } else { $limite = null; }
	
		// On prépare le tableau de rendu
		$contacts = array();
	
		// On récupère les fiches dans la base de données
		if (is_null($limite)) {
			$query = $link->prepare('SELECT `contact_id`, `contact_nom`, `contact_nom_usage`, `contact_prenoms` FROM `contacts` WHERE `contact_id` != :id AND CONCAT_WS(" ", `contact_prenoms`, `contact_nom`, `contact_nom_usage`, `contact_nom`, `contact_prenoms`) LIKE :search ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC');
		} else {
			$query = $link->prepare('SELECT `contact_id`, `contact_nom`, `contact_nom_usage`, `contact_prenoms` FROM `contacts` WHERE `contact_id` != :id AND CONCAT_WS(" ", `contact_prenoms`, `contact_nom`, `contact_nom_usage`, `contact_nom`, `contact_prenoms`) LIKE :search ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC LIMIT 0, ' . $limite);
		}
		$query->bindParam(':id', $_GET['fiche']);
		$query->bindParam(':search', $recherche);
		$query->execute();
		
		// On récupère les résultats et on affiche en retour ces résultats sous format JSON
		$data = $query->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($data);
			
	endif;

?>