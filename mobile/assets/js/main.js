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
	
};

$(document).ready(main);