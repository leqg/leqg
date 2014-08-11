<?php
	// On récupère la ville recherchée avant de renvoyer les options demandées pour le formulaire ou les puces pour la liste
	$return = $_POST['retour'];
	$script = $_POST['script'];
	if (empty($script)) $script = true;
	$ville = $_POST['ville'];
	$rue = $_POST['rue'];
	$contact = $_POST['contact'];
	
	// On lance la recherche
	$ville = $carto->ville($ville);
	$rue = $carto->rue($rue);
	$immeubles = $carto->listeImmeubles($ville['id'], $rue['id']);
	$i = 1;
	
	foreach ($immeubles as $immeuble) :
	
		if ($return == 'liste') echo '<li class="propositionImmeuble" data-ville="' . $ville['id'] . '" data-rue="' . $rue['id'] . '" data-immeuble="' . $immeuble['id'] . '">';
		if ($return == 'form') echo '<option value="' .$immeuble['id']. '"';
		if ($return == 'form' && $i == 1) echo ' selected>'; 
		if ($return == 'form') echo '>';

			echo ucwords(strtolower($immeuble['numero'])) . ' ' . $rue['nom'];
		
		if ($return == 'liste') echo '</li>';
		if ($return == 'form') echo '</option>';
	
	endforeach;
	
	if ($script === true) :
?>
<script>
	$(".propositionImmeuble").click(function(){
		// On récupère les informations sur l'immeuble, la rue et la ville
			var immeuble = $(this).data('immeuble');
			var rue = $(this).data('rue');
			var ville = $(this).data('ville');
			var contact = $("#fiche-electeur").data('fiche');

		// On lance la fonction AJAX qui va modifier le tout
			$.ajax({
				type: 'POST',
				url:	 'ajax.php?script=nouvelle-adresse',
				data: { 'contact': contact, 'ville': ville, 'rue': rue, 'immeuble': immeuble },
				dataType: 'html'
			}).done(function(data){
				window.location.replace("<?php $core->tpl_get_url('fiche', $contact); ?>");
			}).error(function(){
				console.log('ajax: échec');
			});
	});
</script>
<?php endif; ?>