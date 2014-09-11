var administration = function() {
	$('#ajoutCompte').click(function(){
		$('.overlayForm').fadeIn();
		return false;
	});
	
	$('.fermetureOverlay').click(function(){
		$('.overlayForm').fadeOut();
		return false;
	});
	
	$('.ouvrirMenu').click(function(){
		$(this).toggleClass('actif');
		
		return false;
	});
};

$(document).ready(administration);