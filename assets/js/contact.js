var contact = function() {
	// Action de fermeture des overlays
	$('.fermetureOverlay').click(function() {
		$('.overlayForm').fadeOut();
		$('.detail-critere').hide();
		$('input:not([type="submit"])').val('');
		$('input[type="radio"]').attr('checked', false);
	});
	
	
	// Action de fermerture des colonnes latérales
	$('.fermerColonne').click(function() {
		// On ferme toute la colonne latérale
		$('#colonneDroite section').fadeOut().delay(500);
		$('#colonneDroite section:not(.invisible)').fadeIn();
		
		// On annule le clic sur le lien
		return false;
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
		
		// On recherche les informations sur l'événement
		$.getJSON('ajax.php?script=evenement', {evenement: identifiant}, function(data) {
			// On affecte les informations récupérées
			$('#eventTitre').val(data.historique_objet);
			$('#eventType').val(data.historique_type);
			$('#eventDate').val(data.historique_date_fr);
			$('#eventLieu').val(data.historique_lieu);
			$('#eventNotes').val(data.historique_notes);
			$('#evenement').data('evenement', data.historique_id);
			
			// On affiche le bloc
			$('#evenement').fadeIn();
		});
		
		// On annule le lien pour éviter le #
		return false;
	});
	
	
	// Action lors de la création d'un nouvel élément d'historique
	$('.nouvelEvenement').click(function(){
		// On ferme tous les blocs de la colonne latérale
		$('#colonneDroite section').fadeOut();
		
		// On récupère l'ID du contact
		var contact = $('#nomContact').data('fiche');
		
		// On lance la création de l'événement
		$.getJSON('ajax.php?script=evenement-nouveau', { contact: contact }, function(data) {
			// On affiche l'ID dans la case data concernée
			$('#evenement').data('evenement', data.historique_id);
			
			// On vide les éléments du formulaire
			$('#eventTitre').val(data.historique_objet);
			$('#eventType').val(data.historique_type);
			$('#eventDate').val(data.historique_date_fr);
			$('#eventLieu').val(data.historique_lieu);
			$('#eventNotes').val(data.historique_notes);
			
			// On affiche le bloc
			$('#evenement').fadeIn();
		});
		
		// On annule le clic sur le bouton
		return false;
	});
	
	
	// Enregistrement des données formulaire en cas de blur
	function savingForm( evenement , info , value )
	{
		$.post('ajax.php?script=evenement-update', { evenement: evenement , info: info,  value: value });
	}
	
	$('#eventTitre').blur(function(){
		var info = 'historique_objet';
		var value = $(this).val();
		var evenement = $('#evenement').data('evenement');
		savingForm( evenement , info , value );
	});
	
	$('#eventType').blur(function(){
		var info = 'historique_type';
		var value = $(this).val();
		var evenement = $('#evenement').data('evenement');
		savingForm( evenement , info , value );
	});
	
	$('#eventLieu').blur(function(){
		var info = 'historique_lieu';
		var value = $(this).val();
		var evenement = $('#evenement').data('evenement');
		savingForm( evenement , info , value );
	});
	
	$('#eventDate').blur(function(){
		var info = 'historique_date';
		var value = $(this).val();
		var evenement = $('#evenement').data('evenement');
		savingForm( evenement , info , value );
	});
	
	$('#eventNotes').blur(function(){
		var info = 'historique_notes';
		var value = $(this).val();
		var evenement = $('#evenement').data('evenement');
		savingForm( evenement , info , value );
	});
	
	
	// Action à la demande de suppression d'un événement
	$('.supprimerEvenement').click(function(){
		// On demande la confirmation
		if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.'))
		{
			// On récupère l'identifiant de l'événement
			var evenement = $('#evenement').data('evenement');
			
			// On supprime l'événement en question
			$.post('ajax.php?script=evenement-suppression', { evenement: evenement }, function() {
				// On supprime l'élément de la liste des événements
				$('.evenement-' + evenement).remove();
				
				// On retire tout ce qui est affiché dans la colonne droite
				$('#colonneDroite section').fadeOut().delay(500);
				$('#colonneDroite section:not(.invisible)').fadeIn();
			});
		}
		
		// On annule le clic sur le bouton, au cas où celui-ci se trouve dans un formulaire
		return false;
	});
};

$(document).ready(contact);