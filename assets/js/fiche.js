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