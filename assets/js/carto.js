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
					$('.listeCommunes').append('<a class="nostyle ville-' + val.commune_id + '" href=""><li class="demi contact ville"><strong></strong><p></p></li></a>');
					$('.ville-' + val.commune_id).attr('href', 'index.php?page=carto&niveau=communes&code=' + val.md5);
					$('.ville-' + val.commune_id + ' li strong').html(val.commune_nom);
					$('.ville-' + val.commune_id + ' li p').html(val.departement_nom);
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
}


$(document).ready(carto);