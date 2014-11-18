<?php
	
	if (isset($_GET['rue'])) :
	
		// On récupère la liste des immeubles dans cette rue
		$rue = Carto::listeImmeubles( $_GET['rue'] );
		
		// On affiche la liste en JSON
		echo json_encode($rue);
	
	endif;

?>