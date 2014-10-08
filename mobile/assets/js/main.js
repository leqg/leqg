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
		var immeuble = $(this).data('immeuble');
		$(this).fadeOut();
		$('button.report-' + immeuble).fadeIn();
	});
	
	$('button.report').click(function(){
		// On récupère toutes les informations
		var mission = $(this).data('mission');
		var immeuble = $(this).data('immeuble');
		var statut = $(this).data('statut');
		var type = $(this).data('type');
		
		// On envoie les informations au serveur
		$.post('ajax.php?script=reporting', { type: type, mission: mission, immeuble: immeuble, statut: statut });
		
		// On supprime de la liste
		$('li#element-' + immeuble).remove();
	});
	
};

$(document).ready(main);