<?php
	// On récupère la ville recherchée avant de renvoyer les options demandées pour le formulaire ou les puces pour la liste
	$return = $_POST['retour'];
	$ville = $_POST['ville'];
	$rue = $core->formatage_recherche($_POST['rue']);
	
	// On lance la recherche
	$ville = $carto->ville($ville);
	$rues = $carto->recherche_rue($ville['id'], $rue);
	$i = 1;
	
	foreach ($rues as $rue) :
	
		if ($return == 'liste') echo '<li class="propositionRue" data-ville="' . $ville['id'] . '" data-rue="' . $rue['id'] . '">';
		if ($return == 'form') echo '<option value="' .$rue['id']. '"';
		if ($return == 'form' && $i == 1) echo ' selected>'; 
		if ($return == 'form') echo '>';

			echo ucwords(strtolower($rue['nom'])) . ' (' . $ville['nom'] . ')';
		
		if ($return == 'liste') echo '</li>';
		if ($return == 'form') echo '</option>';
	
	endforeach;
?>
<script>
	$(".propositionRue").click(function(){
		var ville = $(this).data('ville'); // On récupère la ville sélectionnée
		var rue = $(this).data('rue'); // On récupère la rue sélectionnée
		var contact = $("#fiche-electeur").data('fiche'); // On récupère les informations sur la fiche demandée
		$("#choixVille").hide(); // On cache l'espace de sélection de la ville
		$("#choixRue").hide(); // On cache l'espace de sélection de la rue

		// On fait une recherche des immeubles correspondant dans la rue sélectionnée
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=recherche-immeuble',
				data: { 'retour': 'liste' , 'contact' : contact , 'ville' : ville , 'rue' : rue },
				dataType: 'html'
			}).done(function(data){
				$("#selectionImmeuble").html(data);
			});
		
		$("#choixImmeuble").show(); // On affiche l'espace de sélection de l'immeuble dans la rue donnée
		$("#selectionImmeuble").data('ville', ville); // On affecte l'information de ville sélectionnée à l'immeuble
		$("#selectionImmeuble").data('rue', rue); // On affecte l'information de rue sélectionnée à l'immeuble
	});
</script>