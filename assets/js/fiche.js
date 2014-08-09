var fiche = function() {
	// javascript relatif au volet latéral
	// état de base des volets : tous cachés sauf premierContact ou historique
		$(".ficheContact div").hide();
		$("#historique").show();
		$("#premierContact").show();
	
	
	// interactions permettant le passage entre les volets
		$("#ajoutInteraction").click(function(){ // Cliquer pour ajouter une première interaction
			$(".ficheContact div").hide(); // On ferme tous les volets
			$("#nouvelleInteraction").show(); // On affiche la création de fiche
		});
	
	
	// script permettant de se rendre directement à un volet suivant les requêtes GET
		if (getURLVar('interaction')) {
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
		
		if (getURLVar('fichier')) { // Si on demande à rajouter un fichier
			$(".ficheContact div").hide(); // On ferme tous les volets
			$("#nouveauFichier").show(); // Pour afficher le formulaire d'envoi
		}
		
	
	// script de sauvegarde du contenu de l'ajout d'interaction
		$("#sauvegarde").click(function(){
			var fiche = $("#form-fiche").val();
			var type = $("#form-type").val();
			var date = $("#form-date").val();
			var lieu = $("#form-lieu").val();
			var themas = $("#form-themas").val();
			var notes = $("#form-notes").val();
			
			$.ajax({
				type:		'POST',
				url:			'ajax.php?script=historique-ajout',
				data:		{	'fiche'  : fiche,
								'type'   : type,
								'date'   : date,
								'lieu'   : lieu,
								'themas' : themas,
								'notes'  : notes },
				dataType:	'html'
			}).done(function(data){
				var destination = 'http://localhost/leqg/index.php?page=fiche&id=' + fiche + '&interaction=' + data;
				$(location).attr('href', destination);
			});
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
