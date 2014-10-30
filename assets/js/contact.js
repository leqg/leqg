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
		
		// On vide certains formulaires et certaines listes automatiquement
		$('#rechercherRue').val('');
		$('#listeRues').html('');
		
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
	$('.listeDesEvenements').on('click', '.accesEvenement', function(){
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
			
			// On ajoute l'événement à la liste des événements
				// En commençant par déclarer le lien vers l'événement
				$('li.nouvelEvenement').after('<a href="#" class="accesEvenement nostyle evenement-' + data.historique_id + '" data-evenement="' + data.historique_md5 + '"></a>');
				
				// À l'intérieur, on ajoute la puce
				$('a.evenement-' + data.historique_id).append('<li class="evenement ' + data.historique_type + ' clic"></li>');
				
				// À l'intérieur de la puce, on ajoute les informations
				$('a.evenement-' + data.historique_id + ' li').append('<small><span>' + data.historique_type_clair + '</span></small>');
				
				if (data.historique_objet.length >= 1)
				{
					$('a.evenement-' + data.historique_id + ' li').append('<strong>' + data.historique_objet + '</strong>');
				}
				else
				{
					$('a.evenement-' + data.historique_id + ' li').append('<strong>Événement sans titre</strong>');
				}
				
				$('a.evenement-' + data.historique_id + ' li').append('<ul class="infosAnnexes"><li class="date">' + data.historique_date_fr + '</li></ul>');
		});
		
		// On annule le clic sur le bouton
		return false;
	});
	
	
	// Enregistrement des données formulaire en cas de blur
	function savingForm( evenement , info , value )
	{
		$.post('ajax.php?script=evenement-update', { evenement: evenement , info: info,  value: value });
		
		if (info == 'historique_objet')
		{
			if (value.length >= 1)
			{
				$('a.evenement-' + evenement + ' strong').html(value);
			}
			else
			{
				$('a.evenement-' + evenement + ' strong').html('Événement');
			}
		}
		else if (info == 'historique_type')
		{
			$('a.evenement-' + evenement + ' > li').removeClass().addClass('evenement').addClass(value);
			$('a.evenement-' + evenement + ' > li small').remove();
		}
		else if (info == 'historique_lieu')
		{
			if (value.length >= 1)
			{
				$('a.evenement-' + evenement + ' li.lieu').html(value);
			}
			else
			{
				$('a.evenement-' + evenement + ' li.lieu').html('Lieu inconnu');
			}
		}
		else if (info == 'historique_date')
		{
			$('a.evenement-' + evenement + ' li.date').html(value);
		}
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
	
	
	// Action à la demande de modification de la date de naissance
	$('li.naissance').click(function() {
		$('#colonneDroite section').fadeOut().delay(500);
		
		$('.modifierNaissance').fadeIn();
		
		return false;
	});
	
	
	// Action à la demande de sauvegarde de la nouvelle date de naissance
	$('.sauvegardeDateNaissance').click(function() {
		var date = $('#dateDeNaissance').val();
		var contact = $('#nomContact').data('fiche');
		
		// On effectue la sauvegarde
		$.post('ajax.php?script=contact-naissance-update', { date: date , contact: contact }, function() {
			// On modifie l'information sur la fiche
			$('li.naissance').html(date);
		});
		
		// On ferme le volet latéral
		$('#colonneDroite section').fadeOut().delay(500);
		$('#colonneDroite section:not(.invisible)').fadeIn();
		
		return false;
	});
	
	
	// Action à la demande de modification des informations d'organisation
	$('li.organisme').click(function() {
		$('#colonneDroite section').fadeOut().delay(500);
		
		$('.modifierOrganisme').fadeIn();
		
		return false;
	});
	
	
	// Action à la demande de sauvegarde de la nouvelle date de naissance
	$('.sauvegarderOrganisation').click(function() {
		var organisation = $('#changerOrganisme').val();
		var fonction = $('#changerFonction').val();
		var contact = $('#nomContact').data('fiche');
		
		// On effectue la sauvegarde
		$.post('ajax.php?script=contact-update-organisation', { contact: contact , organisation: organisation , fonction: fonction }, function() {
			// On modifie l'information sur la fiche
			if (organisation.length >= 1 && fonction.length >= 1)
			{
				$('li.organisme').html(organisation + ' (' + fonction + ')');
			}
			else if (organisation.length >= 1 && fonction.length == 0)
			{
				$('li.organisme').html(organisation);
			}
			else if (organisation.length == 0 && fonction.length >= 1)
			{
				$('li.organisme').html(fonction);
			}
			else
			{
				$('li.organisme').html('<span class="inconnu">Pas d\'organisme renseigné</span>');
			}
		});
		
		// On ferme le volet latéral
		$('#colonneDroite section').fadeOut().delay(500);
		$('#colonneDroite section:not(.invisible)').fadeIn();
		
		return false;
	});
	
	
	// Action à la demande de modification des informations d'organisation
	$('li.adresse').click(function() {
		$('#colonneDroite section').fadeOut().delay(500);
		
		$('.modifierAdresse').fadeIn();
		
		return false;
	});
	
	
	// Action lors de la recherche d'une ville
	$('#rechercherRue').keyup(function() {
		var rue = $(this).val();
		
		if (rue.length >= 3) {
			$.getJSON('ajax.php?script=rues', { 'rue' : rue }, function(data) {
				// On commence par vider la liste des résultats avant d'y installer les nouveaux
				$('#listeRues').html('');
				
				$.each( data, function( key, val ) {
				
					// On créé d'abord une nouvelle puce
					$('#listeRues').append('<li id="rue-' + val.rue_id + '"></li>');
					
					// On ajoute après les différents span
					$('#rue-' + val.rue_id).append('<button class="ajouterLaRue" data-rue="' + val.rue_id + '" data-nom="' + val.rue_nom + '" data-ville="' + val.commune_nom + '">Choisir</button>');
					$('#rue-' + val.rue_id).append('<span class="rue-nom">' + val.rue_nom + '</span>');
					$('#rue-' + val.rue_id).append('<span class="rue-ville">' + val.commune_nom + '</span>');
				});
				
				$('#listeRues').show();
			});
		}
	});
	
	
	// Action lors du choix d'une rue
	$('#listeRues').on('click', '.ajouterLaRue', function() {
		// On récupère les données
		var rue = $(this).data('rue');
		var nom = $(this).data('nom');
		var ville = $(this).data('ville');
		
		// on affecte les données de rue
		$('#rueSelectionImmeuble').val(nom);
		
		// on récupère la liste des immeubles dans la rue
		$.getJSON('ajax.php?script=immeubles', { rue: rue }, function(data) {
			
			$('#listeImmeubles').html('');
			
			$.each( data , function( key , val ) {
				// On créé d'abord une nouvelle puce
				$('#listeImmeubles').append('<li id="immeuble-' + val.id + '"></li>');
				
				// On ajoute après les différents span
				$('#immeuble-' + val.id).append('<button class="choisirImmeuble" data-immeuble="' + val.id + '">Choisir</button>');
				$('#immeuble-' + val.id).append('<span class="rue-immeuble">' + val.numero + ' ' + nom + '</span>');
				$('#immeuble-' + val.id).append('<span class="rue-ville">' + ville + '</span>');
			});
			
			$('#listeImmeubles').show();
			
		});
		
		// On ferme le formulaire ouvert, pour ouvrir celui de sélection d'immeubles
		$('#colonneDroite section').fadeOut().delay(500);
		$('.choixImmeuble').fadeIn();
	});
	
	
	// On prévoit de revenir en arrière lors du choix de l'immeuble si on clique sur le nom de la rue
	$('#rueSelectionImmeuble').click(function() {
		// On ferme tout pour ouvrir le volet sur la sélection de rue
		$('#colonneDroite section').fadeOut().delay(500);
		$('.modifierAdresse').fadeIn();
	});
	
	// Actions effectuée au choix d'un immeuble
	$('#listeImmeubles').on('click', '.choisirImmeuble', function(){
		var immeuble = $(this).data('immeuble');
		var fiche = $('#nomContact').data('fiche');
		
		// On ferme les onglets qui doivent être invisibles
		$('#colonneDroite section').fadeOut().delay(500);
		$('#colonneDroite section:not(.invisible').fadeIn();
		
		// On vide les formulaires
		$('#rechercherRue').val('');
		$('#listeRues').html('');
		$('#rueSelectionImmeuble').val('');
		$('#listeImmeubles').html('');
		
		// On modifie les données dans la base de données
		$.post('ajax.php?script=contact-update-immeuble', { contact: fiche , immeuble: immeuble }, function(data) {
			// On met à jour l'adresse sur le formulaire
			$('li.adresse').html(data);
		});
	});
};

$(document).ready(contact);