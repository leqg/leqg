<?php
	
	if (isset($_GET['rue'])) :
	
		// On formate le contenu pour la recherche
		$recherche = $core->formatage_recherche($_GET['rue']);
		
		// On effectue une recherche pour récuperer les données au format JSON
		$rues = $carto->recherche_rue_json($recherche);
	
		echo $rues;
	
	endif;

?>