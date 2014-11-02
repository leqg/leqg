<?php
	
	if (isset($_GET['ville'])) :
	
		// On formate le contenu pour la recherche
		$recherche = $core->formatage_recherche($_GET['ville']);
		
		// On effectue une recherche pour récuperer les données au format JSON
		$villes = $carto->recherche_ville_json($recherche);
	
		echo $villes;
	
	endif;

?>