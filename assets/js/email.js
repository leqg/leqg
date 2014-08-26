var email = function() {
		
	// Données relative au système d'export
	
		// On commence par cacher le moteur de calcul
		$('#calcul').hide();
		$('#fichier').hide();	
		$('#boutonExportation').hide();
		$('#affichage-envoi').hide();
		$('#estimationCout').hide();
		
		// On prépare ce qu'il se passe quand on clique sur l'estimation du nombre de fiches du formulaire
		$("#export").on('submit', function() {
			// On lance le script AJAX
			var donnees = $(this).serialize();
			
			// On lance le moteur de calcul
			$('#calcul').show();
			
			$.ajax({
				url: $(this).attr('action'),
				type: $(this).attr('method'),
				data: $(this).serialize(),
				dataType: 'html'
			}).done(function(data){
				var cout = data * 0.1;
				var cout = Math.round(cout * 100) / 100;
			
				$('#affichage-envoi').hide();
				$("#affichageEstimation").html(data);
				$("#estimationCout").show();
				$("#affichageCout").html(cout);
				$('#boutonExportation').show();
				$('#fichier').hide();
				$('#calcul').hide();
			});
			
			// On retourne une erreur pour ne pas rediriger vers la page du formulaire
			return false;
		});
		
		
		// On envoi les données pour l'exportation
		$("#exportation").on('click', function() {
			
			// On commence par enlever le bouton d'export pour afficher le calcul en cours et puis le bouton vers le fichier
			$('#calcul').show();
			$('#boutonExportation').hide();
			$('#affichage-envoi').show();
			$('#calcul').hide();
			
			$.ajax({
				url: $(this).attr('href'),
				type: 'POST',
				data: $('#export').serialize(),
				dataType: 'html'
			});
			
			var campagneId = $('#campagne-id').val();
			var destination = 'index.php?page=email&action=historique&campagne=' + campagneId;
			$(location).attr('href', destination);
			
			// On annule le clique sur le lien
			return false;
		});
		
		
		
	// Scripts relative au système de critère géographique de l'export
	
		$('#ville-recherche').keyup(function(){
			var value = $(this).val();
			var ciblage = $(this).data('ciblage');
			
			if (value.length >= 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=email-export-ville',
					data: { 'ville': value, 'ciblage': ciblage },
					dataType: 'html'
				}).done(function(data){
					$('#ville-resultats').html(data).show();
				});
			} else {
				$('#ville-resultats').hide().html('');
			}
		});
	
		$('#rue-recherche').keyup(function(){
			var value = $(this).val();
			var ville = $(this).data('ville');
			var ciblage = $(this).data('ciblage');
			
			if (value.length >= 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=email-export-rue',
					data: { 'ville': ville, 'rue': value, 'ciblage': ciblage },
					dataType: 'html'
				}).done(function(data){
					$('#rue-resultats').html(data).show();
				}).error(function(){
				});
			} else {
				$('#rue-resultats').hide().html('');
			}
		});
	
};

$(document).ready(email);