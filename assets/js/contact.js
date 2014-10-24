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
	
	
	// Affichage du formulaire de recherche de fiches à lier
	$('.ajouterLien').click(function() {
		$('#colonneDroite section').fadeOut().delay(500);
		$('#ChercherFicheALier').fadeIn();
	});
	
	
	// Recherche de fiches à lier
	$('#rechercheFiche').change(function() {
		var recherche = $(this).val();
		var fiche = $('#nomContact').data('fiche');
		
		if (recherche.length >= 3)
		{
			// On commence par vider la liste actuellement affichée
			$('#listeFichesALier').html('');
			
			// On lance la recherche
			$.getJSON('ajax.php?script=fiches', { recherche: recherche, limite: 25, fiche: fiche }).done(function(data) {
				// On lance une boucle pour affecter chaque résultat à la liste
				$.each( data, function( key, val ) {
					$('#listeFichesALier').append('<li id="contact-' + val.contact_id + '"></li>');
					$('#contact-' + val.contact_id).append('<button class="lierLaFiche" data-fiche-a="' + fiche + '" data-fiche-b="' + val.contact_id + '" data-noms="' + val.contact_nom.toUpperCase() + ' ' + val.contact_nom_usage.toUpperCase() + '" data-prenoms="' + val.contact_prenoms.toLowerCase() + '">Choisir</button>');
					$('#contact-' + val.contact_id).append('<span class="contact">' + val.contact_nom.toUpperCase() + ' ' + val.contact_nom_usage.toUpperCase() + '</span><span class="prenoms">' + val.contact_prenoms.toLowerCase() + '</span>');
				});
		
				// On fini par afficher la liste des fiches à lier
				$('#listeFichesALier').show();
			});
		}
		else
		{
			$('#listeFichesALier').hide();
		}
	});
	
	
	// Action lors de la demande de liaison de deux fiches
	$('#ChercherFicheALier').on('click', '.lierLaFiche', function() {
		var ficheA = $(this).data('fiche-a');
		var ficheB = $(this).data('fiche-b');
		var noms = $(this).data('noms');
		var prenoms = $(this).data('prenoms');
		console.log(ficheA);
		console.log(ficheB);
	
		// On commence par enlever le formulaire de choix des fiches à lier
		$('#ChercherFicheALier').fadeOut().delay(500);
		$('#colonneDroite section:not(.invisible)').fadeIn();
		
		// On lance la requête AJAX
		$.post('ajax.php?script=lier-fiches', { ficheA: ficheA, ficheB: ficheB }).done(function() {
			$('.ajouterLien').before('<li class="lien">' + noms + ' ' + prenoms + '</li>');
		});
	});
	
	
	// Action lors de l'ouverture d'un événement d'historique
	$('.accesEvenement').click(function(){
		var identifiant = $(this).data('evenement');
		
		// On ferme tous les blocs de la colonne latérale
		$('#colonneDroite section').fadeOut();
	});
};

$(document).ready(contact);