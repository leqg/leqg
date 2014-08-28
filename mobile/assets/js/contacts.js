var contacts = function() {
	
	// Script d'affichage des interactions
	$('#goToHistorique').click(function(){
		$('#historique').css('left', '0');
		
		// On annule le clic sur le lien
		return false;
	});
	
	// Script de retour sur la fiche
	$('#retourDepuisHistorique').click(function(){
		$('#historique').css('left', '-100%');
		
		// On annule le clic sur le lien
		return false;
	});
	
};

$(document).ready(contacts);