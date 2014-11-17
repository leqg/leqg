<?php
	
	if (isset($_GET['ville'])) :
	
		// On formate le contenu pour la recherche
		$recherche = Core::searchFormat($_GET['ville']);
		
		// On effectue une recherche pour récuperer les données au format JSON
		$villes = Carto::recherche_ville($recherche);
		
		// Pour chaque ville, on cherche des informations sur le département
		foreach ($villes as $key => $ville) {
			// On récupère les informations sur le département
			$departement = Carto::departement($ville['departement_id']);
			
			// On rajoute ces informations aux résultats
			$villes[$key] = array_merge($ville, $departement);
			
			// On rajoute également l'ID de la ville encodé SHA256
			$villes[$key]['md5'] = hash('sha256', $ville['commune_id']);
		}
	
		echo json_encode($villes);
	
	endif;

?>