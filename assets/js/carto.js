var carto = function() {
	// On cache ce qui doit être caché
	$('#listeVilles').hide();
	$('#resultatsCantons').hide();
	$('#affichage-envoi').hide();
	
	// Script concernant la recherche d'une ville dans l'arborescence
	$("#rechercheVille").keyup(function(){
		var recherche = $(this).val();
		
		if (recherche.length > 3) {
			$.getJSON('ajax.php?script=villes', { 'ville': recherche }, function(data) {
				// On cache la liste
				$('.resultats').hide();
				$('.listeCommunes').html('');
				
				// On fait une boucle des différents résultats
				$.each(data, function(key, val) {
					// On créé la puce
					$('.listeCommunes').append('<a class="nostyle ville-' + val.id + '" href=""><li class="demi contact ville"><strong></strong><p>Accès à la fiche</p></li></a>');
					$('.ville-' + val.id).attr('href', 'index.php?page=carto&niveau=communes&code=' + val.id);
					$('.ville-' + val.id + ' li strong').html(val.city);
					$('.ville-' + val.id + ' li p').html(val.country_name);
				});
				
				// On affiche la liste finale
				$('.resultats').fadeIn();
			});
		} else {
			$('.resultats').hide();
		}
	});
	
	
	// Script concernant la recherche d'une rue dans l'arborescence
	$(".rechercheRue").keyup(function(){
		var recherche = $(this).val();
		var ville = $(this).data('ville');
		
		// On vérifie qu'il y a assez de caractères pour lancer la recherche
		if (recherche.length >= 3) {
			$.getJSON('ajax.php?script=rues', { rue: recherche, ville: ville }, function(data) {
				// On vide la liste des rues affichées
				$('.listeDesRues').html('');
				
				// On fait une boucle des rues pour les afficher
				$.each(data, function(key, val) {
					// On rajoute une nouvelle puce dans la liste
					$('.listeDesRues').append('<li class="rue rue-' + val.rue_code + '"><span class="rue-nom"></span><a href=""><button>Explorer</button></a></li>');
					
					// On ajoute les informations à la puce
					$('.listeDesRues .rue-' + val.rue_code + ' span.rue-nom').html(val.rue_nom);
					$('.listeDesRues .rue-' + val.rue_code + ' a').attr('href', 'index.php?page=carto&niveau=rues&code=' + val.rue_code);
				});
				
				// On affiche le bloc des résultats
				$('.resultatsRues').fadeIn();
			});
		} else {
			// On cache le bloc des résultats
			$('.resultatsRues').hide();
			
			// On vide la liste des rues affichées
			$('.listeDesRues').html('');
		}
	});
	
	
	// Script concernant la recherche d'une rue dans l'arborescence
	$(".rechercheBureau").keyup(function(){
		var recherche = $(this).val();
		var ville = $(this).data('ville');
		
		// On vérifie qu'il y a assez de caractères pour lancer la recherche
		if (recherche.length >= 2) {
			$.getJSON('ajax.php?script=bureaux', { bureau: recherche, ville: ville }, function(data) {
				// On vide la liste des rues affichées
				$('.listeDesBureaux').html('');
				
				// On fait une boucle des rues pour les afficher
				$.each(data, function(key, val) {
					// On rajoute une nouvelle puce dans la liste
					$('.listeDesBureaux').append('<li class="bureau bureau-' + val.bureau_code + '"><span class="bureau-nom"></span><a href=""><button>Explorer</button></a></li>');
					
					// On ajoute les informations à la puce
					$('.listeDesBureaux .bureau-' + val.bureau_code + ' span.bureau-nom').html('Bureau ' + val.bureau_numero);
					$('.listeDesBureaux .bureau-' + val.bureau_code + ' a').attr('href', 'index.php?page=carto&niveau=bureau&code=' + val.bureau_code);
				});
				
				// On affiche la liste des bureaux
				$('.listeDesBureaux').show();
				
				// On affiche le bloc des résultats
				$('.resultatsBureaux').fadeIn();
			});
		} else {
			// On cache le bloc des résultats
			$('.listeDesBureaux').hide();
			
			// On vide la liste des rues affichées
			$('.listeDesBureaux').html('');
		}
	});
}


$(document).ready(carto);