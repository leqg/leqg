var administration = function() {
	$('#ajoutCompte').click(function(){
		$('.overlayForm').fadeIn();
	});
	
	$('.fermetureOverlay').click(function(){
		$('.overlayForm').fadeOut();
	});
};

$(document).ready(administration);