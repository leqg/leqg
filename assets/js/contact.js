var contact = function() {
	// Action de fermeture des overlays
	$('.fermetureOverlay').click(function() {
		$('.overlayForm').fadeOut();
		$('.detail-critere').hide();
		$('input:not([type="submit"])').val('');
		$('input[type="radio"]').attr('checked', false);
	});
	
	
	// Ouverture de l'overlay d'ajout de coordonnées
	$('.ajouterCoordonnees').click(function() {
		$('#ajoutCoordonnees').fadeIn();
	});
	
	
	// Sélection du type de coordonnées à ajouter
	$('.selectionType').click(function(){
		var value = $(this).data('type');
		
		$('.detail-critere').hide();
		$('.detail-critere-' + value).show();
	});
	
	
	// Ajout des coordonnées entrées à la base de données
	$('#ajoutDeCoordonnees').submit(function() {
		// On récupère les données
		var action = $(this).attr('action');
		var contact = $('#idFiche').val();
		var type = $('.selectionType:checked').val();
		
		if (type == 'email')
		{
			var coordonnees = $('#form-ajout-email').val();
		}
		else
		{
			var coordonnees = $('#form-ajout-telephone').val();
		}
		
		// On envoie les informations à la base de données
		$.post('ajax.php?script=coordonnees–ajout', { contact: contact, type: type, coordonnees: coordonnees });
		
		// On prépare la fonction d'ajout automatique d'espace
		
		// On ajoute les informations à l'affichage actuel de la page, et si c'est un numéro de téléphone on le retraite pour l'affichage
		if (type != 'email')
		{
			coordonnees = coordonnees.replace(/(.{2})/g, "$1 ");
		}
		var puce = '<li class="' + type + '">' + coordonnees + '</li>';
		$('ul.coordonnees li.ajout').before(puce);
		
		// Si ce n'était pas déjà le cas, on active l'îcone correspondante
		if (type == 'email')
		{
			if ( !$('ul.icones-etatcivil li.email').hasClass('envoyerEmail') )
			{
				$('ul.icones-etatcivil li.email').addClass('envoyerEmail');
			}
		}
		else if (type == 'mobile')
		{
			if ( !$('ul.icones-etatcivil li.sms').hasClass('envoyerSMS') )
			{
				$('ul.icones-etatcivil li.sms').addClass('envoyerSMS');
			}
		}
		
		// On ferme le formulaire en vidant le formulaire
		$('.overlayForm').fadeOut();
		$('.detail-critere').hide();
		$('#form-ajout-email').val('');
		$('#form-ajout-telephone').val('');
		$('.selectionType:checked').attr('checked', false);
		
		// On annule la validation du formulaire
		return false;
	});
};

$(document).ready(contact);