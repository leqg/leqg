var dossier = function() {
	
	function fermeture() {
		$('.droite section').hide();
		$('.droite section:not(.invisible)').fadeIn();
	}
	
	// Action de fermerture des colonnes latérales
	$('.fermerColonne').click(function() {
		fermeture();
		
		// On annule le clic sur le lien
		return false;
	});


	// Fonction d'affichage du formulaire de modification de la description
	$('.modifierDescription').click(function() {
		// On ouvre le volet
		$('.droite section').hide();
		$('.modifDescription').fadeIn();
	});
	
	
	// Script de modification de la description du dossier
	$('.validerModificationDescription').click(function() {
		// On récupère les données du formulaire
		var dossier = $('.titre').data('dossier');
		var description = $('#modificationDescription').val();
		
		// On modifie la description
		$.post('ajax.php?script=dossier-description', { description: description, dossier: dossier }, function() {
			$('.description p').html(description);
			
			// On ferme le volet
			fermeture();
		});
		
		return false;
	});


	// Fonction d'affichage du formulaire de modification du titre
	$('.titre').dblclick(function() {
		// On ouvre le volet
		$('.droite section').hide();
		$('.modifTitre').fadeIn();
	});
	
	
	// Script de modification du titre du dossier
	$('.validerModificationTitre').click(function() {
		// On récupère les données du formulaire
		var dossier = $('.titre').data('dossier');
		var titre = $('#modificationTitre').val();
		
		// On modifie la description
		$.post('ajax.php?script=dossier-titre', { titre: titre, dossier: dossier }, function() {
			$('.titre').html(titre);
			
			// On ferme le volet
			fermeture();
		});
		
		return false;
	});

};

$(document).ready(dossier);