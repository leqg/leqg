<?php
	
	if (isset($_GET['rue'])) :
	
		// On formate le contenu pour la recherche
		$recherche = preg_replace('#[^A-Za-z]#', '%', $_GET['rue']);
		
		// On effectue une recherche pour récuperer les données au format JSON et on les affiche
		echo Carto::recherche_rue_json($recherche);
	
	endif;

?>