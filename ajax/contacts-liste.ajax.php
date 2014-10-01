<?php
	// On récupère la liste des tris
	if (isset($_GET['tri'])) :
	
		// On récupère la liste des tris
		$tri = $_GET['tri'];
		
		// On retraite la liste des tris sous forme d'un tableau d'arguments $args
		$tri = explode(',', $tri);
		$args = array();
		foreach($tri as $key => $t) :
			$t = explode(':', $t);
			$args[$t[0]] = $t[1];
		endforeach;
		
		// On lance la recherche des fiches correspondances aux arguments
		$fiches = $fiche->liste( 'JSON' , $args );
		
	else :
		
		return false;
	
	endif;
?>