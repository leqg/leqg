<?php
	
	if (isset($_GET['bureau'])) :
	
		// On formate le contenu pour la recherche
		$recherche = preg_replace('#[^[:alnum:]]#', '%', $_GET['bureau']);
		
		// On effectue une recherche pour récuperer les données au format JSON
		$bureaux = Carto::recherche_bureau_json($recherche);
	
		echo $bureaux;
	
	endif;

?>