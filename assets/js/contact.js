var contact = function() {
	// Action de fermeture des overlays
	$('.fermetureOverlay').click(function() {
		$('.overlayForm').fadeOut();
		$('.detail-critere').hide();
		$('input:not([type="submit"])').val('');
		$('input[type="radio"]').attr('checked', false);
	});
	
	
	// Action au chargement de la page d'ouverture directe d'un événement
	$.urlParam = function(name) {
	    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	    if (results==null){
	       return null;
	    }
	    else{
	       return results[1] || 0;
	    }
    }
	if ($.urlParam('evenement'))
	{
		var idEvenement = $.urlParam('evenement');
		
		// On simule le clic sur l'événement
		chargementEvenement($('.evenement-' + idEvenement));
	}
	
	
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
	
	
	// Ouverture du volet de modification du nom
	$('#nomContact').click(function() {
		// On ferme toute la colonne latérale
		$('#colonneDroite section').hide();
		$('.changerNom').fadeIn();
	});
	
	
	// Modification du nom
	$('.validerChangementNom').click(function() {
		// On récupère les données
		var fiche = $('.titre').data('fiche');
		var nom = $('#changerNomFamille').val();
		var nomUsage = $('#changerNomUsage').val();
		var prenoms = $('#changerPrenoms').val();
		
		// On sauvegarde la modification
		$.post('ajax.php?script=contact-nom', { fiche: fiche, nom: nom, nomUsage: nomUsage, prenoms: prenoms }, function() {
			// On modifie les informations dans le haut de page
			if (nomUsage == '')
			{
				$('.titre').html('<span>' + nom + '</span> <span>' + prenoms + '</span>');
			}
			else
			{
				$('.titre').html('<span>' + nom + '</span> <span>' + nomUsage + '</span> <span>' + prenoms + '</span>');
			}
			
			// On ferme les données
			$('#colonneDroite section').hide();
			$('#colonneDroite section:not(.invisible)').fadeIn();
		});
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
	
	
	// Fonction de chargement de l'événement
	function chargementEvenement(eConcerne)
	{
		var identifiant = eConcerne.data('evenement');
		
		// On commence par vider la liste des fichiers
		$('ul.listeDesFichiers li:not(.nouveauFichier)').remove();
		$('ul.listeDesTaches li:not(.nouvelleTache)').remove();
		
		// On commence par vider le dossier ouvert
		$('.afficherInfosDossier').hide();
		$('.lierDossier').show();
		
		// On ferme tous les blocs de la colonne latérale
		$('#colonneDroite section').hide();
		
		// On recherche les informations sur l'événement
		$.getJSON('ajax.php?script=evenement', { evenement: identifiant }, function(data) {
			// On affecte les informations récupérées
			$('#eventTitre').val(data.historique_objet);
			$('#eventType').val(data.historique_type);
			$('#eventDate').val(data.historique_date_fr);
			$('#eventLieu').val(data.historique_lieu);
			$('#eventNotes').val(data.historique_notes);
			$('#evenement').data('evenement', data.historique_id);
			$('#formEvenement').val(data.historique_id);
			
			// On regarde s'il y a un dossier et si oui, on l'affiche
			if (data.dossier_id)
			{
    			    $('.lierDossier').hide();
    			    
    			    // On parse les informations du dossier
    			    var infosDossier = $.parseJSON(data.dossier);
    			    
    			    // On affiche les informations du dossier
    			    $('.afficherInfosDossier').attr('href', 'index.php?page=dossier&dossier=' + infosDossier.dossier_md5);
    			    $('.afficherInfosDossier li strong').html(infosDossier.dossier_nom);
    			    $('.afficherInfosDossier li em').html(infosDossier.dossier_description);
    			    $('.afficherInfosDossier').show();
			}
			
			// On va formater la liste des fichiers pour l'ajouter à la fiche événement
			var fichiers = $.parseJSON(data.fichiers);
			
			// On fait une boucle des fichiers à afficher
			$.each(fichiers, function(key, val) {
				// On créé d'abord une nouvelle puce à la fin de la liste
				$('.nouveauFichier').after('<a href="uploads/' + val.fichier_url + '" class="fichier-' + val.fichier_id + '" target="_blank"><li class="fichier"><strong></strong><em></em></li></a>');
				
				// On ajoute les données importantes dans dans la puce
				$('.fichier-' + val.fichier_id + ' li strong').html(val.fichier_nom);
				$('.fichier-' + val.fichier_id + ' li em').html(val.fichier_description);
			});
			
			// On va formater la liste des tâches pour l'ajouter à la fiche événement
			var taches = $.parseJSON(data.taches);
			
			// On fait une boucle des tâches à afficher
			$.each(taches, function(key, val) {
				// On créé d'abord une nouvelle puce à la fin de la liste
				$('.nouvelleTache').after('<li class="tache tache-' + val.tache_id + '" data-tache="' + val.tache_id + '"><strong>' + val.tache_description + '</strong></li>');
			});
			
			// On affiche le bloc
			$('#evenement').fadeIn();
		});
	}
	
	
	// Action lors de l'ouverture d'un événement d'historique
	$('.listeDesEvenements').on('click', '.accesEvenement', function(){
		var eventConcerne = $(this);
		
		// On déclenche l'action de chargement de l'événement
		chargementEvenement(eventConcerne);
		
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
			$('#evenement').data('evenement', data.historique_id);
			$('#formEvenement').val(data.historique_id);
		
        		// On commence par vider le dossier ouvert
        		$('.afficherInfosDossier').hide();
        		$('.lierDossier').show();
			
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
			
			// On fabrique les variables
			var splitVal, day, month, year;
			
			// On calcule l'âge
			splitVal = date.split('/');
			
			if (splitVal.length < 3)
			{
				$('.age').html('<span class="inconnu">Âge inconnu</span>');
				return;
			}
			
			day = splitVal[0];
			month = splitVal[1];
			year = splitVal[2];
			
			if (year.length < 4 || month.length > 2 || day.length > 2 || month.length == 0 || day.lenght == 0)
			{
				$('.age').html('<span class="inconnu">Âge inconnu</span>');
				return;
			}
			
			dob = new Date(year, month-1, day);
			
			age = new Age(dob).getAge();
			$('.age').html(age.years + ' ans');
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
	
	
	// Action à la demande de sauvegarde de la nouvelle organisation et de sa fonction au sein de l'entreprise
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
		
		// On retire la carte Google Maps jusqu'au rechargement de la page pour éviter d'avoir une carte non à jour
		$('#carte').addClass('invisible');

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
	
	
	// Script d'affichage du formulaire d'ajout de tag
	$('.ajouterTag').click(function(){
		$(this).hide();
		$('.formulaireTag').fadeIn();
		$('.formulaireTag input').focus();
	});
	
	
	// Script de retour en arrière
	$('.formulaireTag input').blur(function(){
		$('.formulaireTag').hide();
		$('.ajouterTag').fadeIn();
	});
	
	
	// Script d'ajout du tag lors de l'appui sur Entrée
	$('.formulaireTag input').keyup(function(e){
		if (e.keyCode == 13)
		{
			// On récupère le tag entré
			var tag = $('.formulaireTag input').val();
			var contact = $('#nomContact').data('fiche');
			
			// On sauvegarde le tag entré
			$.post('ajax.php?script=contact-tag-nouveau', { contact: contact , tag: tag }, function() {
				// On ajoute le tag à la liste
				$('.ajouterTag').before('<li class="tag" data-tag="' + tag + '">' + tag + '</li>');
				
				// On retire le formulaire
				$('.formulaireTag').hide();
				$('.formulaireTag input').val('');
				$('.ajouterTag').fadeIn();
			});
		}
	});
	
	
	// Script de suppression d'un tag, par double clic
	$('.listeDesTags').on('dblclick', '.tag', function() {
		// On récupère les données
		var tag = $(this).data('tag');
		var contact = $('#nomContact').data('fiche');
		
		// On supprime le tag de la base
		$.post('ajax.php?script=contact-tag-supprimer', { contact: contact , tag: tag }, function() {
			// On supprime le tag de la liste
			$('.tag[data-tag=' + tag + ']').remove();
		});
	});
	
	
	// Script de chargement du formulaire d'ajout de fichier
	$('.nouveauFichier').click(function() {
		// On ferme l'événement ouvert
		$('#evenement').hide();
		
		// On vide le formulaire d'ajout de fichier
		$('#formFichier').val('');
		$('#formFichierTitre').val('');
		$('#formFichierDesc').val('');
		
		// On affiche le formulaire d'ajout de fichier
		$('.ajoutFichier').fadeIn();
	});
	
	
	// Script de chargement du formulaire d'événement
	$('.revenirEvenement').click(function() {
		// On ferme l'ajout de fichier
		$('.ajoutFichier').hide();
		$('.ajouterTache').hide();
		
		// On vide le formulaire d'ajout de fichier
		$('#formFichier').val('');
		$('#formFichierTitre').val('');
		$('#formFichierDesc').val('');
		$('#formAjoutTache').val('');
		
		// On affiche la fiche événement
		$('#evenement').fadeIn();
	});
	
	
	// Script d'affichage du formulaire d'ajout de tâche
	$('.nouvelleTache').click(function() {
		// On affiche le formulaire
		$('#colonneDroite section').hide();
		$('.ajouterTache').fadeIn();
	});
	
	
	// Script d'ajout SQL de la tâche
	$('.validerTache').click(function() {
		// On récupère les données des formulaires
		var tache = $('#formAjoutTache').val();
		var contact = $('#nomContact').data('fiche');
		var user = $('#formDestinataireTache').val();
		var evenement = $('#evenement').data('evenement');
		
		// On lance l'enregistrement
		$.post('ajax.php?script=contact-tache-nouvelle', { contact: contact, tache: tache, user: user, evenement: evenement }, function(data) {
			data = $.parseJSON(data);
			
			$.each(data, function(key, val) {
				// On ajoute la tâche à la liste des tâches
				$('.nouvelleTache').after('<li class="tache tache-' + val.tache_id + '" data-tache="' + val.tache_id + '"><strong>' + val.tache_description + '</strong><em>' + val.user + '</em></li>');
			});
			
			// On revient en arrière pour revenir à l'événement
			$('.ajouterTache').hide();
			$('#formAjoutTache').val('');
			$('#evenement').fadeIn();
		});
		
		return false;
	});
	
	
	// Script de suppression d'une tâche
	$('.listeDesTaches').on('click', '.tache', function() {
		// On récupère le numéro de la tâche
		var evenement = $('#evenement').data('evenement');
		var task = $(this).data('tache');
		
		// On lance la suppression de la tâche
		$.post('ajax.php?script=contact-tache-suppression', { evenement: evenement, tache: task }, function() {
			// On supprime la puce
			$('.tache-' + task).remove();
		});
	});
	
	
	// Ouverture de la fenêtre de changement d'email
	$('.coordonnees').on('click', 'li', function() {
		// On récupère le type de coordonnée
		var type = $(this).attr('class');
		
		// On récupère l'ID des coordonnées demandée et la valeur actuelle
		var id = $(this).data('id');
		var val = $(this).html();
		
		// On intégre ces valeurs dans le formulaire
		$('.modifier-' + type).data('id', id);
		$('.modifier-' + type + ' input').val(val);
		
		// On affiche le formulaire
		$('#colonneDroite section').hide();
		$('.modifier-' + type).fadeIn();
	});
	
	
	// Script de suppression d'une coordonnée
	$('.supprimerCoordonnee').click(function() {
		var type = $(this).data('type');
		var id = $('.modifier-' + type).data('id');
		
		// On enregistre la suppression dans la base de données
		$.post('ajax.php?script=contact-suppression-coord', { id: id }, function() {
			// On supprime l'élément de la liste
			$('#' + type + '-' + id).remove();
			
			// On ferme le volet latéral
			$('#colonneDroite section').hide();
			$('#colonneDroite section:not(.invisible)').fadeIn();
			
			// On vide le formulaire de modification
			$('.modifier-' + type + ' input').val('');
		});
	});
	
	
	// Script d'affichage du module d'envoi de SMS
	$('.envoyerSMS').click(function() {
    	    // On vide au cas où le formulaire d'envoi de SMS
    	    $('#messageSMS').val('');
    	    
    	    // On affiche le formulaire
    	    $('#colonneDroite section').hide();
    	    $('.envoi-sms').fadeIn();
	});
	
	
	// Script d'envoi du SMS entré
	$('.SMSsending').click(function() {
    	    // On récupère les paramètres entrés
    	    var contact = $('#nomContact').data('fiche');
    	    var numero = $('#choixNumero').val();
    	    var message = $('#messageSMS').val();
    	    
    	    // On lance l'envoi du SMS
    	    $.post('ajax.php?script=contact-sms', { contact: contact, numero: numero, message: message }, function(){
        	    // On ajoute le SMS à la liste des événements
        	    $('.nouvelEvenement').after('<li class="evenement sms"><strong>Envoi d\'un SMS</strong><ul class="infosAnnexes"><li class="date">Maintenant</li></ul></li>');
			
			// On vide le formulaire
			$('#messageSMS').val('');
			
			// On ferme la colonne
			$('#colonneDroite section').hide();
			$('#colonneDroite section:not(.invisible)').fadeIn();	
    	    });
	});
	
	
	// Script d'affichage du module d'envoi de mail
	$('.envoyerEmail').click(function() {
    	    // On vide au cas où le formulaire d'envoi de SMS
    	    $('#messageEmail').val('');
    	    
    	    // On affiche le formulaire
    	    $('#colonneDroite section').hide();
    	    $('.envoi-email').fadeIn();
	});
	
	
	// Script d'envoi du SMS entré
	$('.EmailSending').click(function() {
    	    // On récupère les paramètres entrés
    	    var contact = $('#nomContact').data('fiche');
    	    var adresse = $('#choixAdresse').val();
    	    var objet = $('#objetEmail').val();
    	    var message = $('#messageEmail').val();
    	    
    	    // On lance l'envoi du SMS
    	    $.post('ajax.php?script=contact-email', { contact: contact, adresse: adresse, objet: objet, message: message }, function(){
        	    // On ajoute le SMS à la liste des événements
        	    $('.nouvelEvenement').after('<li class="evenement email"><strong>Envoi d\'un courrier électronique</strong><ul class="infosAnnexes"><li class="date">Maintenant</li></ul></li>');
			
			// On vide le formulaire
			$('#objetEmail').val('');
			$('#messageEmail').val('');
			
			// On ferme la colonne
			$('#colonneDroite section').hide();
			$('#colonneDroite section:not(.invisible)').fadeIn();	
    	    });
	});
	
	
	// Script de suppression d'une adresse connue
	$('.supprimerAdresse').click(function() {
    	    // On récupère le numéro de la fiche
    	    var fiche = $('#nomContact').data('fiche');
    	    
    	    // On lance la suppression de la fiche
    	    $.post('ajax.php?script=contact-adresse-suppression', { fiche: fiche }, function() {
        	    // On va retirer l'information affichée
        	    $('.etatcivil .adresse').html('<span class="inconnu">Adresse inconnue</span>');
        	    
        	    // On ferme le volet
        	    $('#colonneDroite section').hide();
        	    $('#colonneDroite section:not(.invisible)').fadeIn();
    	    });
    	    
    	    return false;
	});
	
	
	// Script de changement de sexe
	$('.sexe').click(function() {
		var fiche = $('#nomContact').data('fiche');
		$.post('ajax.php?script=contact-sexe', { fiche: fiche });
		
		if ($(this).hasClass('homme'))
		{
			$(this).removeClass('homme');
			$(this).addClass('femme');
			$(this).html('Femme');
		}
		else if ($(this).hasClass('femme'))
		{
			$(this).removeClass('femme');
			$(this).addClass('inconnu');
			$(this).html('Sexe');
		}
		else
		{
			$(this).removeClass('inconnu');
			$(this).addClass('homme');
			$(this).html('Homme');
		}
	});
	
	
	// Script d'affichage de la liaison de dossiers
	$('.lierDossier').click(function() {
    	    // On ferme les onglets
    	    $('#colonneDroite section').hide();
    	    $('.selectionDossier').fadeIn();
	});
	
	
	// Script d'affichage de l'ajout de dossier
	$('.ajoutDossier').click(function() {
    	    // On ferme les onglets
    	    $('#colonneDroite section').hide();
    	    $('.creationDossier').fadeIn();
	});
	
	
	// Script de retour au choix des dossiers
	$('.revenirDossier').click(function() {
    	    // On ferme les onglets
    	    $('#colonneDroite section').hide();
    	    $('.selectionDossier').fadeIn();
	});
	
	
	// Script de création du dossier
	$('.creerDossier').click(function() {
    	    // On récupère les informations
    	    var nom = $('#creationDossierNom').val();
    	    var desc = $('#creationDossierDesc').val();
    	    var event = $('#evenement').data('evenement');
    	    
    	    // On enregistre le dossier
    	    $.getJSON('ajax.php?script=dossier-creer', { nom: nom, desc: desc, event: event }, function(data) {
        	    // On rajoute le dossier à la liste
        	    $('.listeDesDossiers .ajoutDossier').after('<a href="index.php?page=dossier&dossier=' + data.dossier_md5 + '"><li class="dossier dossier-' + data.dossier_id + '" data-dossier="' + data.dossier_id + '"><strong></strong><em></em></li></a>');
        	    $('dossier-' + data.dossier_id + ' strong').html(data.dossier_nom);
        	    $('dossier-' + data.dossier_id + ' em').html(data.dossier_description);
        	    
        	    // On modifie le dossier dans la fiche evenement
        	    $('.affichageDossier').html('<li class="dossier"><strong>' + data.dossier_nom + '</strong><em>' + data.dossier_description + '</em></li>');
    	    
            // On ferme la fenètre
            $('.creationDossier').hide();
            
            // On vide le formulaire
            $('#creationDossierNom').val('');
            $('#creationDossierDesc').val('');
            
            // On revient à l'événement
            $('#colonneDroite section').hide();
            $('#evenement').fadeIn();
    	    });
    	    
    	    return false;
	});
	
	
	// Script du au choix d'un dossier
	$('.choixDossier').click(function() {
    	    var dossier = $(this).data('dossier');
    	    var evenement = $('#evenement').data('evenement');
    	    
    	    // On lie les deux éléments
    	    $.getJSON('ajax.php?script=dossier-lier', { dossier: dossier, evenement: evenement }, function(data) {
        	    // On rajoute le dossier à la liste
        	    $('.listeDesDossiers .ajoutDossier').after('<a href="index.php?page=dossier&dossier=' + data.dossier_md5 + '"><li class="dossier dossier-' + data.dossier_id + '" data-dossier="' + data.dossier_id + '"><strong></strong><em></em></li></a>');
        	    $('dossier-' + data.dossier_id + ' strong').html(data.dossier_nom);
        	    $('dossier-' + data.dossier_id + ' em').html(data.dossier_description);
        	    
        	    // On modifie le dossier dans la fiche evenement
        	    $('.lierDossier').hide();
        	    $('.afficherInfosDossier').show();
        	    $('.afficherInfosDossier').attr('href', 'index.php?page=dossier&dossier=' + data.dossier_md5);
        	    $('.afficherInfosDossier li').html('<strong>' + data.dossier_nom + '</strong><em>' + data.dossier_description + '</em>');
    	    
            // On ferme la fenètre
            $('.creationDossier').hide();
            
            // On revient à l'événement
            $('#colonneDroite section').hide();
            $('#evenement').fadeIn();
    	    });
	});
};

$(document).ready(contact);



/**
 * Helper to find the age of a date based on a start date and an end date
 *
 * @param {Date} date1
 * @param {Date} [date2] If not passed the current date will be used.
 * @constructor
 */
 
function Age(date1, date2){
	this.date1 = date1;
	this.date2 = date2 || new Date();
	this.age = 0;
	
	/**
	* Get the number of years between the 2 dates
	*
	* return {Number}
	*/
	this.getYears = function getYears() {
		return Math.floor(this.age);
	};
	
	/**
	* Get the number of months past the difference in years between the two dates.
	* e.g. If the difference is 18 months then this will return 6.0
	*
	* return {Number}
	*/
	this.getMonths = function getMonths() {
		// Take the absolute age in years and use the remainder
		// to figure out how many months into that year we are.
		return Math.floor((this.age - this.getYears()) * 12 *10)/10
	};
	
	/**
	* Get the age between the two dates expressed as an object containing the years and months
	*
	* @returns {{years: {Number}, months: {Number}}}
	*/
	this.getAge = function getAge() {
		return { years: this.getYears(), months: this.getMonths() };
	};
	
	/**
	* Calculate the difference between the two dates and return them
	* as a fraction expression of years
	*/
	this.setAge = function setAge() {
		var diff;
		diff = this.date2.getTime() - this.date1.getTime();
		this.age = diff / (1000 * 60 * 60 * 24 * 365.25);
	};
	
	/**
	* Initialise
	*/
	this.init = function init() {
		this.setAge();
	};
	this.init();
}