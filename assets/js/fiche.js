var fiche = function() {
	// javascript relatif au volet latéral
	// état de base des volets : tous cachés sauf premierContact ou historique
		$(".ficheContact div").hide();
		$("#historique").show();
		$("#premierContact").show();
		$("#resultats").hide();
		$("#resultatsRue").hide();

	
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
		if (getURLVar('interaction') && !getURLVar('fichier') && !getURLVar('modifier') && !getURLVar('dossier') && !getURLVar('creerDossier')) {
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
		
	
	// script permettant la recherche de doublons lors de la création d'une fiche et l'affichage des actions associées
		/*$("#creerFiche").click(function(){
			// On récupère les données du formulaire
				var nom = $("#form-creation-nom").val();
				var nomUsage = $("#form-creation-nom-usage").val();
				var prenom = $("#form-creation-prenom").val();
				var sexe = $("#form-sexe").val();
				var fixe = $("#form-fixe").val();
				var mobile = $("#form-mobile").val();
				var email = $("#form-email").val();
			
			// On lance la fonction AJAX
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=verification-doublons',
				data: { 'nom': nom, 'nom-usage': nomUsage, 'prenom': prenom, 'sexe': sexe, 'fixe': fixe, 'mobile': mobile, 'email': email },
				dataType: 'html'
			}).done(function(data){
				$("#creationNouvelleFiche").html(data);
			}).error(function(){
				$("#creationNouvelleFiche").html('<h3>Erreur lors de la création d\'une nouvelle fiche');
			});
		});*/
		
		// script permettant de choisir une fiche à laquelle ajouter les informations entrées dans le script d'ajout de données avant de rediriger vers la fiche en question
			$(document).on('click', '.existante', function(){
				var contact = $(this).data('contact');
				var fixe = $("#selectionDoublons").data('fixe');
				var email = $("#selectionDoublons").data('email');
				var mobile = $("#selectionDoublons").data('mobile');
				
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=fusion-donnees-nouvelle-fiche',
					data: { 'contact': contact, 'fixe': fixe, 'mobile': mobile, 'email': email },
					dataType: 'html'
				}).done(function(){
					var destination = 'http://localhost/leqg/index.php?page=fiche&id=' + contact;
					$(location).attr('href', destination);
				}).error(function(){
					console.log('ajax: erreur');
				});
			});
		
		// script permettant de créer une nouvelle fiche si aucun doublon n'a été validé
			$(document).on('click', '#nouvelleFiche', function(){
				var nom = $("#selectionDoublons").data('nom');
				var nomUsage = $("#selectionDoublons").data('nomUsage');
				var prenom = $("#selectionDoublons").data('prenom');
				var sexe = $("#selectionDoublons").data('sexe');
				var fixe = $("#selectionDoublons").data('fixe');
				var email = $("#selectionDoublons").data('email');
				var mobile = $("#selectionDoublons").data('mobile');
				
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=nouvelle-fiche-adresse',
					data: { 'nom': nom, 'nomUsage': nomUsage, 'prenom': prenom, 'sexe': sexe, 'fixe': fixe, 'email': email, 'mobile': mobile },
					dataType: 'html'
				}).done(function(data){
					$("#creationNouvelleFiche").html(data);
					$("#resultat-ville").hide();
				}).error(function(){
					console.log('ajax: erreur');
				});
			});
			
			$(document).on('keyup', '#form-recherche-ville', function(){
				var ville = $("#form-recherche-ville").val();
				
				if (ville.length > 3) {
					$.ajax({
						type: 'POST',
						url: 'ajax.php?script=recherche-ville',
						data: { 'retour': 'liste', 'script': 'false', 'ville': ville },
						dataType: 'html'
					}).done(function(data){
						$("#resultat-ville").show();
						$("#resultat-ville ul").html(data);
					}).error(function(){
						$("#resultat-ville").hide();
					});
				} else {
					$("#resultat-ville").hide();
				}
			});
			
			$(document).on('click', '#resultat-ville ul .propositionVille', function(){
				var nom = $("#fiche-electeur").data('nom');
				var nomUsage = $("#fiche-electeur").data('nomUsage');
				var prenom = $("#fiche-electeur").data('prenom');
				var sexe = $("#fiche-electeur").data('sexe');
				var fixe = $("#fiche-electeur").data('fixe');
				var email = $("#fiche-electeur").data('email');
				var mobile = $("#fiche-electeur").data('mobile');
				var ville = $(this).data('id');
				
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=nouvelle-fiche-rue',
					data: { 'ville': ville, 'nom': nom, 'nomUsage': nomUsage, 'prenom': prenom, 'sexe': sexe, 'fixe': fixe, 'email': email, 'mobile': mobile },
					dataType: 'html'
				}).done(function(data){
					$("#creationNouvelleFiche").html(data);
					$("#resultat-rue").hide();
				}).error(function(){
					console.log('ajax: erreur');
				});
			});
			
			$(document).on('keyup', '#form-recherche-rue', function(){
				var rue = $("#form-recherche-rue").val();
				var ville = $("#fiche-electeur").data('ville');
				
				if (rue.length > 3) {
					$.ajax({
						type: 'POST',
						url: 'ajax.php?script=recherche-rue',
						data: { 'retour': 'liste', 'script': 'false', 'ville': ville, 'rue': rue },
						dataType: 'html'
					}).done(function(data){
						$("#resultat-rue").show();
						$("#resultat-rue ul").html(data);
					}).error(function(){
						$("#resultat-rue").hide();
					});
				} else {
					$("#resultat-rue").hide();
				}
			});
			
			$(document).on('click', '#selectionRue .nouvelleRue', function() {
				var nom = $("#fiche-electeur").data('nom');
				var nomUsage = $("#fiche-electeur").data('nomUsage');
				var prenom = $("#fiche-electeur").data('prenom');
				var sexe = $("#fiche-electeur").data('sexe');
				var fixe = $("#fiche-electeur").data('fixe');
				var email = $("#fiche-electeur").data('email');
				var mobile = $("#fiche-electeur").data('mobile');
				var ville = $(this).data('ville');
				var rue = $(this).data('rue');

				// On commence par l'AJAX d'ajout de la rue
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=ajout-rue',
					data: { 'ville': ville, 'rue': rue },
					dataType: 'html'
				}).done(function(rue){
					console.log(rue);
					// on exécute maintenant la requête AJAX permettant d'afficher la création de l'immeuble dans la rue
					//$.ajax({
					//	type: 'POST',
					//	url: 'ajax.php?script=nouvelle-fiche-creer-immeuble',
					//	data: { 'rue': rue, 'ville': ville, 'nom': nom, 'nomUsage': nomUsage, 'prenom': prenom, 'sexe': sexe, 'fixe': fixe, 'email': email, 'mobile': mobile },
					//	dataType: 'html'
					//});
				}).error(function(){ console.log('ajax: erreur'); });
			});
			
			$(document).on('click', '#resultat-rue ul .propositionRue', function(){
				var nom = $("#fiche-electeur").data('nom');
				var nomUsage = $("#fiche-electeur").data('nomUsage');
				var prenom = $("#fiche-electeur").data('prenom');
				var sexe = $("#fiche-electeur").data('sexe');
				var fixe = $("#fiche-electeur").data('fixe');
				var email = $("#fiche-electeur").data('email');
				var mobile = $("#fiche-electeur").data('mobile');
				var ville = $(this).data('ville');
				var rue = $(this).data('rue');
				
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=nouvelle-fiche-immeuble',
					data: { 'rue': rue, 'ville': ville, 'nom': nom, 'nomUsage': nomUsage, 'prenom': prenom, 'sexe': sexe, 'fixe': fixe, 'email': email, 'mobile': mobile },
					dataType: 'html'
				}).done(function(data){
					$("#creationNouvelleFiche").html(data);
					$("#resultat-immeuble").hide();
				}).error(function(){
					console.log('ajax: erreur');
				});
			});
			
			$(document).on('keyup', '#form-recherche-immeuble', function(){
				var immeuble = $("#form-recherche-immeuble").val();
				var rue = $("#fiche-electeur").data('rue');
				var ville = $("#fiche-electeur").data('ville');
				
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=recherche-immeuble',
					data: { 'retour': 'liste', 'script': 'false', 'ville': ville, 'rue': rue, 'immeuble': immeuble },
					dataType: 'html'
				}).done(function(data){
					$("#resultat-immeuble").show();
					$("#resultat-immeuble ul").html(data);
				}).error(function(){
					$("#resultat-immeuble").hide();
				});
			});
			
			$(document).on('click', '#resultat-immeuble ul .propositionImmeuble', function(){
				var nom = $("#fiche-electeur").data('nom');
				var nomUsage = $("#fiche-electeur").data('nomUsage');
				var prenom = $("#fiche-electeur").data('prenom');
				var sexe = $("#fiche-electeur").data('sexe');
				var fixe = $("#fiche-electeur").data('fixe');
				var email = $("#fiche-electeur").data('email');
				var mobile = $("#fiche-electeur").data('mobile');
				var ville = $(this).data('ville');
				var rue = $(this).data('rue');
				var immeuble = $(this).data('immeuble');
				
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=creer-fiche',
					data: { 'immeuble': immeuble, 'rue': rue, 'ville': ville, 'nom': nom, 'nomUsage': nomUsage, 'prenom': prenom, 'sexe': sexe, 'fixe': fixe, 'email': email, 'mobile': mobile },
					dataType: 'html'
				}).done(function(data){
					var destination = 'http://localhost/leqg/index.php?page=fiche&id=' + data;
					$(location).attr('href', destination);
				}).error(function(){
					console.log('ajax: erreur');
				});
			});
			
			
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
					$("#resultats").show();
					$("#liste-villes").html(data);
				}).error(function(){
					console.log('Erreur AJAX');
				});
			} else {
				$("#resultats").hide();
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
