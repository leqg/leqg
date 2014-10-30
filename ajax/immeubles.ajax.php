<?php
	
	if (isset($_GET['rue'])) :
	
		// On récupère la liste des immeubles dans cette rue
		$rue = $carto->listeImmeubles( $_GET['rue'] );
		
		// On affiche la liste en JSON
		echo json_encode($rue);
	
	endif;

?>