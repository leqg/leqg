var main = function() {
	
	// Script d'affichage du menu
	$('#goToMenu').click(function(){
		if ($(this).attr('class') == 'rotate') {
			$('nav#menu').css('top', '-225px');
			$('header#barreHaute').css('top', '0px');
			$(this).removeClass('rotate');
		} else {
			$('nav#menu').css('top', '0px');
			$('header#barreHaute').css('top', '225px');
			$(this).addClass('rotate');
		}
		
		// On annule le clic sur le lien
		return false;
	});
	
	
	// Script de reporting
	$('button.choix').click(function(){
		var type = $(this).data('type');
		
		if (type == 'porte') {
			var id = $(this).data('electeur');
		} else {
			var id = $(this).data('immeuble');
		}
		
		$(this).fadeOut();
		$('button.report-' + id).fadeIn();
	});
	
	$('button.report').click(function(){
		// On récupère toutes les informations
		var mission = $(this).data('mission');
		var statut = $(this).data('statut');
		var type = $(this).data('type');
		
		if (type == 'porte') {
			var id = $(this).data('electeur');
		} else {
			var id = $(this).data('immeuble');
		}
		
		// On envoie les informations au serveur
		$.post('ajax.php?script=reporting', { type: type, mission: mission, id: id, statut: statut });
		
		// On supprime de la liste
		$('li#element-' + id).remove();
	});
	
};

$(document).ready(main);