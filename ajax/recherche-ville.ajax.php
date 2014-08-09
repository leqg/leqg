<?php
	// On récupère la ville recherchée avant de renvoyer les options demandées pour le formulaire ou les puces pour la liste
	$return = $_POST['retour'];
	$ville = $core->formatage_recherche($_POST['ville']);
	
	// On lance la recherche
	$villes = $carto->recherche_ville($ville);
	$i = 1;
	
	foreach ($villes as $ville) :
	
		if ($return == 'liste') echo '<li class="propositionVille" data-id="' . $ville['id'] . '">';
		if ($return == 'form') echo '<option value="' .$ville['id']. '"';
		if ($return == 'form' && $i == 1) echo ' selected>'; 
		if ($return == 'form') echo '>';

			echo ucwords(strtolower($ville['nom'])) . ' (' . $ville['departement_id'] . ')';
		
		if ($return == 'liste') echo '</li>';
		if ($return == 'form') echo '</option>';
	
	endforeach;
?>
<script>
	$(".propositionVille").click(function(){
		var ville = $(this).data('id'); // On récupère la ville sélectionnée
		$("#choixVille").hide(); // On cache l'espace de sélection de la ville
		$("#choixRue").show(); // On affiche l'espace de sélection de la rue
		$("#selectionRue").data('ville', ville); // On affecte l'information de ville sélectionnée à la rue
	});
</script>