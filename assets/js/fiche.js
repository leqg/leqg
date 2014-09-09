var fiche = function() {
	// javascript relatif au volet latéral
	// état de base des volets : tous cachés sauf premierContact ou historique
		$(".ficheContact div").hide();
		$("#historique").show();
		$("#premierContact").show();
		$("#resultatsRue").hide();
		$("#resultatsVille").hide();
		$("#resultats").hide();
		$('#listeDesDossiers').hide();
		$('#nouvelleTache').hide();
		
	
	// On cache tout ce qui doit être caché
		$(".choix").hide();
		$("#choixDeVilleDeNaissance").hide();

	
	// scripts permettant d'afficher en style "vide" tous les champs de formulaire non remplis de la page principale de la fiche
		function couleurVide() {
			if ($("#form-email").val() == '') { $("#form-email").addClass('vide'); } else { $("#form-email").removeClass('vide'); }
			if ($("#form-telephone").val() == '') { $("#form-telephone").addClass('vide'); } else { $("#form-telephone").removeClass('vide'); }
			if ($("#form-mobile").val() == '') { $("#form-mobile").addClass('vide'); } else { $("#form-mobile").removeClass('vide'); }
		}
		
		// fonction lancée au démarrage de la page
		$("#form-email").ready(couleurVide);
		$("#form-telephone").ready(couleurVide);
		$("#form-mobile").ready(couleurVide);
		
		// fonction lancée à chaque modification
		$("#form-email").change(couleurVide);
		$("#form-telephone").change(couleurVide);
		$("#form-mobile").change(couleurVide);
		
	
	
	// script permettant de se rendre directement à un volet suivant les requêtes GET
		if (getURLVar('interaction') && !getURLVar('fichier') && !getURLVar('modifier') && !getURLVar('dossier') && !getURLVar('creerDossier') && !getURLVar('changementNaissance') && !getURLVar('modifierInformations') && !getURLVar('envoyerSMS') && !getURLVar('envoyerEmail') && !getURLVar('afficherDossiers') && !getURLVar('ajoutTache')) {
			var interaction = getURLVar('interaction'); // On récupère l'ID de l'interaction demandée
			
			$(".ficheContact div").hide(); // On ferme tous les volets
			
			// On charge les informations liées à l'interaction sélectionnées
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=interaction-chargement',
				data: { id: interaction },
				dataType: 'html'
			}).done(function(data){
				$("#interaction").html(data);
			});
			
			$("#interaction").show(); // On affiche la fenêtre d'interaction demandée
		}
		
		else if (getURLVar('fichier')) { // Si on demande à rajouter un fichier
			$(".ficheContact div").hide(); // On ferme tous les volets
			$("#nouveauFichier").show(); // Pour afficher le formulaire d'envoi
		}
		
		else if (getURLVar('modifier')) { // Si on demande à modifier la fiche interaction
			$(".ficheContact div").hide(); // On ferme tous les volets
			$("#modifierFiche").show();
		}
		
		else if (getURLVar('modifierAdresse')) {
			$(".ficheContact div").hide();
			$("#changementAdresse").show();
		}
		
		else if (getURLVar('modifierRue')) {
			$(".ficheContact div").hide();
			$("#changementRue").show();
		}
		
		else if (getURLVar('modifierImmeuble')) {
			$(".ficheContact div").hide();
			$("#changementImmeuble").show();
		}
		
		else if (getURLVar('creerImmeuble')) {
			$(".ficheContact div").hide();
			$("#creerImmeuble").show();
		}
		
		else if (getURLVar('nouvelleInteraction')) {
			$(".ficheContact div").hide();
			$("#nouvelleInteraction").show();
		}
		
		else if (getURLVar('dossier')) {
			$(".ficheContact div").hide();
			$("#lierUnDossier").show();
		}
		
		else if (getURLVar('creerDossier')) {
			$(".ficheContact div").hide();
			$("#creerUnDossier").show();
		}
		
		else if (getURLVar('changementNaissance')) {
			$(".ficheContact div").hide();
			$("#changementNaissance").show();
		}
		
		else if (getURLVar('modifierInformations')) {
			$(".ficheContact div").hide();
			$("#changementEtatCivil").show();
		}
		
		else if (getURLVar('envoyerSMS')) {
			$('.ficheContact div').hide();
			$('#nouveauSMS').show();
		}
		
		else if (getURLVar('envoyerEmail')) {
			$('.ficheContact div').hide();
			$('#nouveauEmail').show();
		}
		
		else if (getURLVar('afficherDossiers')) {
			$('.ficheContact div').hide();
			$('#listeDesDossiers').show();
		}
		
		else if (getURLVar('ajoutTache')) {
			$('.ficheContact div').hide();
			$('#nouvelleTache').show();
		}
		
	
	// script de sauvegarde du contenu de l'ajout d'interaction
		$("#sauvegarde").click(function(){
			var fiche = $("#form-fiche").val();
			var type = $("#form-type").val();
			var date = $("#form-date").val();
			var lieu = $("#form-lieu").val();
			var objet = $("#form-objet").val();
			var notes = $("#form-notes").val();
			
			$.ajax({
				type:		'POST',
				url:			'ajax.php?script=historique-ajout',
				data:		{	'fiche'  : fiche,
								'type'   : type,
								'date'   : date,
								'lieu'   : lieu,
								'objet'  : objet,
								'notes'  : notes },
				dataType:	'html'
			}).done(function(data){
				var destination = 'index.php?page=fiche&id=' + fiche + '&interaction=' + data;
				$(location).attr('href', destination);
			});
		});

	
	// script permettant de lancer la procédure de changement de sexe
		$("#modifierSexe").click(function(){
			var fiche = getURLVar('id'); // On récupère l'ID de l'interaction demandée
			
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=changement-sexe',
				data: { 'fiche': fiche },
				dataType: 'html'
			}).done(function(data){
				var destination = 'index.php?page=fiche&id=' + fiche;
				$(location).attr('href', destination);
			});	
		});
	
	
	// script permettant d'afficher à la manière d'une fiche les informations NOM et Prénom sur la création de fiche
		var miseAJour = function(){
			var nom = $("#form-creation-nom").val();
			var nomUsage = $("#form-creation-nom-usage").val();
			var prenom = $("#form-creation-prenom").val();
			
			if (nom != '' || nomUsage != '' || prenom != '') {
				$("h2.titre").html('<span class="nom">' + nom + '</span><span class="nomUsage">' + nomUsage + '</span><span>' + prenom + '</span>');
			} else {
				$("h2.titre").html('Création d\'une fiche');
			}
			
			return true;
		};
		
		$("#form-creation-nom").keyup(miseAJour);
		$("#form-creation-nom-usage").keyup(miseAJour);
		$("#form-creation-prenom").keyup(miseAJour);
					
			
	// Script de recherche d'une nouvelle ville
		$("#changementAdresse-rechercheVille").keyup(function(){
			var ville = $(this).val();
			var fiche = $(this).data('fiche');
			
			if (ville.length > 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=modification-ville',
					data: { 'fiche': fiche, 'ville': ville },
					dataType: 'html'
				}).done(function(data){
					$("#resultatsVille").show();
					$("#liste-villes").html(data);
				}).error(function(){
					console.log('Erreur AJAX');
				});
			} else {
				$("#resultatsVille").hide();
			}
		});
			
			
	// Script de recherche d'une nouvelle rue
		$("#changementAdresse-rechercheRue").keyup(function(){
			var rue = $(this).val();
			var fiche = $(this).data('fiche');
			var ville = $(this).data('ville');
			
			if (rue.length > 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=modification-rue',
					data: { 'fiche': fiche, 'ville': ville, 'rue': rue },
					dataType: 'html'
				}).done(function(data){
					$("#resultatsRue").show();
					$("#liste-rues").html(data);
				}).error(function(){
					console.log('Erreur AJAX');
				});
			} else {
				$("#resultatsRue").hide();
			}
		});
	
	// script d'ajout d'une rue lors de la recherche
		$('body').on('click', '#ajoutRue', function(){
			// On récupère les variables sur le lien
			var rue = $(this).data('rue');
			var ville = $(this).data('ville');
			var fiche = $(this).data('fiche');
			
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=ajout-rue',
				data: { 'ville': ville, 'rue': rue },
				dataType: 'html'
			}).done(function(id){
				var destination = 'index.php?page=fiche&id=' + fiche + '&creerImmeuble=true&rue=' + id + '&ville=' + ville;
				$(location).attr('href', destination);
			}).error(function(){
				alert('Erreur à la construction de la rue, contactez l\'équipe technique');
			});
			
			// On fini par annuler le clique
			return false;
		});
		
	
	// choix de la ville de naissance
		$("#villeNaissance").keyup(function(){
			var entree = $(this).val();
			
			if (entree.length > 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=ville-naissance',
					data: { 'ville': entree },
					dataType: 'html'
				}).done(function(data){
					$('.choix').show();
					$('#liste-villeNaissance').html(data);
				}).error(function(){
					console.log('Ajax : error');
				});
			} else {
				$('.choix').hide();
			}
		});
		
		
	// clique sur la ville de naissance pour la sélectionner
		$('aside').on('click', '.propositionVilleNaissance', function(){
			var ville = $(this).data('ville');
			var affichage = $(this).data('nom');
			
			$('.choix').hide();
			$('#liste-villeNaissance').html('');
			$('#villeNaissance').val('');
			$('#villeChoisieAuFinal').val(ville);
			$('#nomVilleChoisie').html(affichage);
			$('#choixDeVilleDeNaissance').show();
		});
	
	
	// script d'estimation du coût d'un SMS
		$("#form-sms").keyup(function(){
			var message = $(this).val();
			var taille = message.length;
			var tailleSMS = 160;
			
			var nombreExact = taille / tailleSMS;
			var nombre = Math.ceil(nombreExact);
			
			$('#estimation-sms').html(nombre);
		})
	
	
	// script d'affichage de l'ajout de tag
		$('#ajouterTag').click(function(){
			$(this).hide();
			$('#formulaireTag').show();
			$('#tagAjout').focus();
		});
	
	
	// script d'enregistrement d'un nouveau tag pour une fiche donnée
		$('#validerTag').click(function(){
			var fiche = $(this).data('fiche');
			var tag = $('#tagAjout').val();
			
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=fiche-ajouttag',
				data: { 'fiche': fiche, 'tag': tag },
				dataType: 'html'
			}).done(function(data){
				// On ajoute d'abord le tag à la liste des tags
				$('#listesTags').append('<span class="tag" id="tag-' + $.now() + '">' + tag + '</span>');
				
				// On retire après l'affichage du formulaire et on le vide pour réafficher le bouton d'ajout
				$('#tagAjout').blur();
				$('#formulaireTag').hide();
				$('#tagAjout').val('');
				$('#ajouterTag').show();
			}).error(function(){
				$('#formulaireTag').hide();
				$('#ajouterTag').show();
			});
		});
		
		$('#tagAjout').bind('keypress', function(e){
			if (e.keyCode == 13) { // Si la touche entrée a été pressée sur le formulaire
				var fiche = $('#tagAjout').data('fiche');
				var tag = $('#tagAjout').val();
				
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=fiche-ajouttag',
					data: { 'fiche': fiche, 'tag': tag },
					dataType: 'html'
				}).done(function(data){
					// On ajoute d'abord le tag à la liste des tags
					$('#listesTags').append('<span class="tag" id="tag-' + $.now() + '">' + tag + '</span>');
					
					// On retire après l'affichage du formulaire et on le vide pour réafficher le bouton d'ajout
					$('#tagAjout').blur();
					$('#formulaireTag').hide();
					$('#tagAjout').val('');
					$('#ajouterTag').show();
				}).error(function(){
					$('#formulaireTag').hide();
					$('#ajouterTag').show();
				});
			}
			else if (e.keyCode == 27) { // Si la touche échap a été pressée sur le formulaire
				$('#tagAjout').blur();
				$('#formulaireTag').hide();
				$('#tagAjout').val('');
				$('#ajouterTag').show();
			}
		});
		
		$(document).bind('keypress', function(e) {
			if (e.keyCode == 84) {
				$('#tagAjout').click();
			}
		});
		
		$('#listesTags').on('click', '.tag', function(){
			// On supprime un tag sur lequel on a cliqué
			var tag = $(this).html();
			var id = $(this).attr('id');
			var id = '#' + id;
			var fiche = $('.listeTags').data('fiche');
			
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=fiche-removetag',
				data: { 'tag': tag, 'fiche': fiche },
				dataType: 'html'
			});
			
			$(id).remove();
		});
		
	
	// Recherche de fiche pour fusion
		$('#form-fiche1').keyup(function(){
			var recherche = $(this).val();
			
			if (recherche.length >= 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=recherche-fusion&fiche=1',
					data: { 'recherche': recherche },
					dataType: 'html'
				}).done(function(data){
					$('#resultats-fiche1').html(data);
				});
			}
		});

		$('#form-fiche2').keyup(function(){
			var recherche = $(this).val();
			var fiche1 = $(this).data('fiche1');
			
			if (recherche.length >= 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=recherche-fusion&fiche=2',
					data: { 'recherche': recherche, 'fiche1': fiche1 },
					dataType: 'html'
				}).done(function(data){
					$('#resultats-fiche2').html(data);
				});
			}
		});
};

$(document).ready(fiche);


// Script relatif à l'affichage de la carte par utilisateur
function initialize() {
	// On récupère les data liées à la carte
	var nom_electeur = $("#carte").data('nom');
	var adresse_electeur = $("#carte").data('adresse');

	geocoder = new google.maps.Geocoder();

	var latlng = new google.maps.LatLng(48.58476, 7.750576);
	var mapOptions = {
		//center: latlng,
		disableDefaultUI: true,
		draggable: true,
		rotateControl: false,
		scrollwheel: false,
		zoomControl: true,
		zoom: 16
	};
	var map = new google.maps.Map(document.getElementById("carte"), mapOptions);

	
	// On marque les différents bâtiments
	// L'adresse à rechercher
	
	var GeocoderOptions = { 'address': adresse_electeur, 'region': 'FR' };
	
	// La function qui va traiter le résultat
	function GeocodingResult(results, status) {
		// Si la recherche a fonctionnée
		if (status == google.maps.GeocoderStatus.OK) {
			// On créé un nouveau marker sur la map
			markerAdresse = new google.maps.Marker({
				position: results[0].geometry.location,
				map: map,
				title: nom_electeur
			});
			
			// On centre sur ce marker
			map.setCenter(results[0].geometry.location);
		}
	}
	
	// On lance la recherche de l'adresse
	geocoder.geocode(GeocoderOptions, GeocodingResult);
	
}

google.maps.event.addDomListener(window, 'load', initialize);
