var porte = function() {
	
	// On cache tout ce qui doit être caché au premier abord
		$('#liste-ville').hide();
		$('#liste-rue').hide();
		
	// On décoche toutes les sélections d'immeuble au démarrage
		$('.checkImmeuble').attr('checked', false);
	
	// Script de recherche de la ville d'une mission
		$('#recherche-ville').keyup(function(){
			var ville = $(this).val();
			
			if (ville.length >= 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=boite-recherche-ville',
					data: { 'ville': ville },
					dataType: 'html'
				}).done(function(data){
					$('#resultats-ville').html(data);
					$('#liste-ville').show();
				});
			} else {
				$('#liste-ville').hide();
			}
		});
	
	// Script de recherche de la rue d'une mission
		$('#recherche-rue').keyup(function(){
			var rue = $(this).val();
			var ville = $(this).data('ville');
			
			if (rue.length >= 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=boite-recherche-rue',
					data: { 'ville': ville, 'rue': rue },
					dataType: 'html'
				}).done(function(data){
					$('#resultats-rue').html(data);
					$('#liste-rue').show();
				});
			} else {
				$('#liste-rue').hide();
			}
		});
	
	// Script de sélection des immeubles
		$('.checkImmeuble').change(function(){
			var value = $(this).val();
			
			$('#labelImmeuble-' + value).toggleClass('choisi');
		});
		
		$('#toutSelect').click(function(){
			$('.checkImmeuble').attr('checked', 'checked');
		});
	
};

$(document).ready(porte);