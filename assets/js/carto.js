var carto = function() {
	// On cache ce qui doit être caché
	$('#listeVilles').hide();
	$('#resultatsCantons').hide();
	$('#affichage-envoi').hide();
	
	// Script concernant la recherche d'une ville dans l'arborescence
	$("#recherche").keyup(function(){
		var recherche = $(this).val();
		
		if (recherche.length > 3) {
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=arborescence-recherche-ville',
				data: { 'recherche': recherche },
				dataType: 'html'
			}).done(function(data){
				$("#listeVilles").show();
				$("#listeVilles").html(data);
			}).error(function(){
				console.log('Ajax: erreur');
			});
		} else {
			$("#listeVilles").hide();
		}
	});
	
	
	// Script concernant la recherche d'une rue dans l'arborescence
	$("#rechercheRue").keyup(function(){
		var recherche = $(this).val();
		var ville = $(this).data('ville');
		
		$.ajax({
			type: 'POST',
			url: 'ajax.php?script=arborescence-recherche-rue',
			data: { 'recherche': recherche, 'ville': ville },
			dataType: 'html'
		}).done(function(data){
			$("#listeRues").show();
			$("#listeRues").html(data);
		}).error(function(){
			console.log('Ajax: erreur');
		});
	});
	
	
	// Données relative au système d'export
	
		// On commence par cacher le moteur de calcul
		$('#calcul').hide();
		$('#fichier').hide();	
		$('#boutonExportation').hide();
		$('#affichage-envoi').hide();
		
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
				$('#affichage-envoi').hide();
				$("#affichageEstimation").html(data);
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
			}).done(function(data){
				//$('#affichage-envoi').html(data);
			});
			
			// On annule le clique sur le lien
			return false;
		});
		
		
	// Données relatives à la recherche d'un nouveau canton
		$('#rechercheCanton').keyup(function(){
			// On commence par récupérer le contenu du formulaire
			var canton = $(this).val();
			var bureau = $(this).data('bureau');
			
			// On vérifie qu'il possède au moins trois caractères
			if (canton.length >= 3) {
			
				// On lance la recherche de cantons
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=recherche-canton',
					data: { 'canton': canton, 'bureau': bureau },
					dataType: 'html'
				}).done(function(data){
					$('#listeCantons').html(data);
					$('#resultatsCantons').show();
				}).error(function(){
					$('#resultatsCantons').hide();
				})
				
			} else {
				$('#resultatsCantons').hide();
			}
		});
}


$(document).ready(carto);