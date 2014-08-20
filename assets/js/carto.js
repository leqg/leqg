var carto = function() {
	// On cache ce qui doit être caché
	$("#listeVilles").hide();
	
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
	
}

$(document).ready(carto);