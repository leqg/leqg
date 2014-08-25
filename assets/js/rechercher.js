var rechercher = function() {
	
	// On lance la recherche dans les différents blocs
	var recherche = $('#recherche').val();
	
		// recherche de fiches
		$.ajax({
			type: 'POST',
			url: 'ajax.php?script=recherche-fiches',
			data: { 'recherche': recherche },
			dataType: 'html'
		}).done(function(data){
			$('#fichesTrouvees').html(data);
		});
		
		
		// recherche d'interactions
		$.ajax({
			type: 'POST',
			url: 'ajax.php?script=recherche-interactions',
			data: { 'recherche': recherche },
			dataType: 'html'
		}).done(function(data){
			$('#interactionsTrouvees').html(data);
		});
		
		
		// recherche de dossiers
		$.ajax({
			type: 'POST',
			url: 'ajax.php?script=recherche-dossiers',
			data: { 'recherche': recherche },
			dataType: 'html'
		}).done(function(data){
			$('#dossiersTrouves').html(data);
		});
		
		
		// recherche de fichiers
		$.ajax({
			type: 'POST',
			url: 'ajax.php?script=recherche-fichiers',
			data: { 'recherche': recherche },
			dataType: 'html'
		}).done(function(data){
			$('#fichiersTrouves').html(data);
		});
	
};

$(document).ready(rechercher);