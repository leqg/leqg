<?php
	
	if (isset($_GET['bureau'])) :
	
		// On formate le contenu pour la recherche
		$recherche = $core->formatage_recherche($_GET['bureau']);
		
		// On effectue une recherche pour récuperer les données au format JSON
		$bureaux = $carto->recherche_bureau_json($recherche);
	
		echo $bureaux;
	
	endif;

?>