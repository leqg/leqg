var contacts = function() {
	$('#modification').hide();
	
	// Script d'affichage des interactions
	$('#goToHistorique').click(function(){
		$('#historique').css('left', '0');
		
		// On annule le clic sur le lien
		return false;
	});
	
	// Script d'affichage du formulaire de modification
	$('#goToModif').click(function(){
		$('#modification').show().css('right', '0');
		
		// On annule le clic sur le lien
		return false;
	});
	
	// Script de retour sur la fiche
	$('#retourDepuisHistorique').click(function(){
		$('#historique').css('left', '-100%');
		
		// On annule le clic sur le lien
		return false;
	});
	
	// Script de retour sur la fiche
	$('#retourDepuisModif').click(function(){
		$('#modification').css('right', '-100%');
		$('#modification').hide();
		
		// On annule le clic sur le lien
		return false;
	});
	
};

$(document).ready(contacts);