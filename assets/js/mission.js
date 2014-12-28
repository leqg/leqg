var mission = function() {
	// Recherche d'une rue pour l'ajout à une mission
	$('#rechercheRue').keyup(function(){
		var rue = $(this).val();
		var mission = $('.titre').data('mission');
		
		if (rue.length >= 3) {
			$.getJSON('ajax.php?script=rues', { 'rue' : rue }, function(data) {
				// On commence par vider la liste des résultats avant d'y installer les nouveaux
				$('#listeRues').html('');
				
				$.each( data, function( key, val ) {
				
					// On créé d'abord une nouvelle puce
					$('#listeRues').append('<li id="rue-' + val.rue_id + '"></li>');
					
					// On ajoute après les différents span
					$('#rue-' + val.rue_id).append('<a href="ajax.php?script=mission-ajout-rue&code=' + mission + '&rue=' + val.rue_id + '" class="nostyle"><button>Choisir</button></a>');
					$('#rue-' + val.rue_id).append('<span class="rue-nom">' + val.rue_nom + '</span>');
					$('#rue-' + val.rue_id).append('<span class="rue-ville">' + val.commune_nom + '</span>');
				});
				
				$('#listeRues').show();
			});
		} else {
    		$('#listeRues').hide();
		}
	});
	
	// Recherche d'une rue pour l'ajout à une mission
	$('#rechercheBureau').keyup(function(){
		var bureau = $(this).val();
		var mission = $('.titre').data('mission');
		
		if (bureau.length >= 2) {
			$.getJSON('ajax.php?script=bureaux', { 'bureau' : bureau }, function(data) {
				// On commence par vider la liste des résultats avant d'y installer les nouveaux
				$('#listeBureaux').html('');
				
				$.each( data, function( key, val ) {
				
					// On créé d'abord une nouvelle puce
					$('#listeBureaux').append('<li id="bureau-' + val.bureau_id + '"></li>');
					
					// On ajoute après les différents span
					$('#bureau-' + val.bureau_id).append('<a href="ajax.php?script=mission-ajout-bureau&code=' + mission + '&bureau=' + val.bureau_id + '" class="nostyle"><button>Ajouter</button></a>');
					$('#bureau-' + val.bureau_id).append('<span class="bureau-nom">Bureau ' + val.bureau_code + ' ' + val.bureau_nom + '</span>');
					$('#bureau-' + val.bureau_id).append('<span class="bureau-ville">' + val.commune_nom + '</span>');
				});
				
				$('#listeBureaux').show();
			});
		} else {
    		$('#listeBureaux').hide();
		}
	});
};

$(document).ready(mission);