var contacts = function() {
	
	// Comportement au clic sur l'ajout de critère
	$('.selectionCritere').click(function() {
		var critere = $(this).data('critere');
		$('.detail-critere').hide();
		$('.detail-critere-' + critere).show();
	});
	
	// Comportement à la validation de l'ajout de critère
	$('#form-ajoutCritere').submit(function() {
		// On récupère les informations du formulaire
		var action = $(this).attr('action');
		var triActu = $('#summaryTri').val();
		var critere = $('input[name=critere]:checked').val();
		if (critere == 'bureau') {
			var tri = $('#listeBureau').val();
			var affichage = $('#listeBureau option:selected').data('numero');
		} else if (critere == 'tags') {
			var tri = $('#tagDemande').val();
			var affichage = tri;
		} else {
			var tri = $('input[name=' + critere + ']:checked').val();
			var affichage = tri;
		}
		
		// On réinitialise les choix
		$('#form-ajoutCritere input[type=radio]').prop('checked', false);
		
		// On recache le choix de tri par critère qui s'est affiché
		$('.detail-critere').hide();
		
		// On prépare les arguments de tri
		if (triActu != '') {
			triActu = triActu + ',' + critere + ':' + tri;
		} else {
			triActu = critere + ':' + tri;
		}
		
		// On ajout les nouveaux critères de tri dans le input[type=hidden] de résumé
		$('#summaryTri').val(triActu);
		
		// On retire les fiches affichées dans le tableau pour y mettre un message d'attente
		var messageAttente = 'Chargement des fiches en cours...';
		$('#majListeFiches').html('<tr><td colspan="6" class="messageAttente">' + messageAttente + '</td></tr>');
		
		// On ajoute le tag à la liste des critères
		$('#criteres').append('<span class="tag" data-critere="' + critere + ':' + tri + '">' + critere + ':' + affichage + '</span>');
		
		// On ferme la fenètre
		$('.overlayForm').fadeOut();
		
		// On récupère la liste JSON des fiches à afficher
		$.getJSON(action, { 'tri': triActu }, function(data){
			// On va traiter les données JSON pour afficher chaque ligne du tableau :)
			$('#majListeFiches').html('');
			$.each(data, function() {
				// On retraite certaines données
				if (this['email'] == null) { this['email'] = '&nbsp;'; }
				if (this['mobile'] == null) { this['mobile'] = '&nbsp;'; } else { this['mobile'] = this['mobile'].substr(0, 2) + ' ' + this['mobile'].substr(2, 2) + ' ' +  this['mobile'].substr(4, 2) + ' ' +  this['mobile'].substr(6, 2) + ' ' +  this['mobile'].substr(8, 2); }
				if (this['telephone'] == null) { this['telephone'] = '&nbsp;'; } else { this['telephone'] = this['telephone'].substr(0, 2) + ' ' + this['telephone'].substr(2, 2) + ' ' +  this['telephone'].substr(4, 2) + ' ' +  this['telephone'].substr(6, 2) + ' ' +  this['telephone'].substr(8, 2); }
				if (this['tags'] == null) { this['tags'] = '&nbsp;'; }
			
				var col1 = '<td><div class="radio"><input type="checkbox" name="fiche-' + this['id'] + '" id="fiche-' + this['id'] + '"><label for="fiche-' + this['id'] + '"><span><span></span></span></label></div></td>';
				var col2 = '<td><a href="index.php?page=fiche&id=' + this['id'] + '">' + this['nom'] + ' ' + this['nom_usage'] + ' ' + this['prenoms'] + '</a></td>';
				var col3 = '<td>' + this['email'] + '</td>';
				var col4 = '<td>' + this['mobile'] + '</td>';
				var col5 = '<td>' + this['telephone'] + '</td>';
				var col6 = '<td>' + this['tags'] + '</td>';
				
				$('#majListeFiches').append('<tr>' + col1 + col2 + col3 + col4 + col5 + col6 + '</tr>');
			});
			
			// On fini par reformater la taille des colonnes
			$('table#listeFiches tbody td:nth-of-type(1)').width(colonne1 - 2);
			$('table#listeFiches tbody td:nth-of-type(2)').width(colonne2);
			$('table#listeFiches tbody td:nth-of-type(3)').width(colonne3 - 5);
			$('table#listeFiches tbody td:nth-of-type(4)').width(colonne4);
			$('table#listeFiches tbody td:nth-of-type(5)').width(colonne5);
			$('table#listeFiches tbody td:nth-of-type(6)').width(colonne6 - 3);
		});
				
		// On évite le lancement du formulaire
		return false;
	});
	
		
	// Comportement à la suppression d'un critère
	$('#criteres').on('click', '.tag', function() {
		var critere = $(this).data('critere');
		var tri = $('#summaryTri').val() + ',';
		
		console.log('On vire : ' + critere + ' de ' + tri );
		
		// On retire le critère demandé
		var tri = tri.replace(critere + ',', '');
		var tri = tri.slice(0, -1);
		
		// On met à jour la liste des tris en cours
		$('#summaryTri').val(tri);
		
		// On retire les fiches affichées dans le tableau pour y mettre un message d'attente
		var messageAttente = 'Chargement des fiches en cours...';
		$('#majListeFiches').html('<tr><td colspan="6" class="messageAttente">' + messageAttente + '</td></tr>');

		// On retire le tag demandé
		$(this).remove();
		
		// On récupère la liste JSON des fiches à afficher
		$.getJSON('ajax.php?script=contacts-liste', { 'tri': tri }, function(data){
			// On va traiter les données JSON pour afficher chaque ligne du tableau :)
			$('#majListeFiches').html('');
			$.each(data, function() {
				// On retraite certaines données
				if (this['email'] == null) { this['email'] = '&nbsp;'; }
				if (this['mobile'] == null) { this['mobile'] = '&nbsp;'; } else { this['mobile'] = this['mobile'].substr(0, 2) + ' ' + this['mobile'].substr(2, 2) + ' ' +  this['mobile'].substr(4, 2) + ' ' +  this['mobile'].substr(6, 2) + ' ' +  this['mobile'].substr(8, 2); }
				if (this['telephone'] == null) { this['telephone'] = '&nbsp;'; } else { this['telephone'] = this['telephone'].substr(0, 2) + ' ' + this['telephone'].substr(2, 2) + ' ' +  this['telephone'].substr(4, 2) + ' ' +  this['telephone'].substr(6, 2) + ' ' +  this['telephone'].substr(8, 2); }
				if (this['tags'] == null) { this['tags'] = '&nbsp;'; }
			
				var col1 = '<td><div class="radio"><input type="checkbox" name="fiche-' + this['id'] + '" id="fiche-' + this['id'] + '"><label for="fiche-' + this['id'] + '"><span><span></span></span></label></div></td>';
				var col2 = '<td><a href="index.php?page=fiche&id=' + this['id'] + '">' + this['nom'] + ' ' + this['nom_usage'] + ' ' + this['prenoms'] + '</a></td>';
				var col3 = '<td>' + this['email'] + '</td>';
				var col4 = '<td>' + this['mobile'] + '</td>';
				var col5 = '<td>' + this['telephone'] + '</td>';
				var col6 = '<td>' + this['tags'] + '</td>';
				
				$('#majListeFiches').append('<tr>' + col1 + col2 + col3 + col4 + col5 + col6 + '</tr>');
			});
			
			// On fini par reformater la taille des colonnes
			$('table#listeFiches tbody td:nth-of-type(1)').width(colonne1 - 2);
			$('table#listeFiches tbody td:nth-of-type(2)').width(colonne2);
			$('table#listeFiches tbody td:nth-of-type(3)').width(colonne3 - 5);
			$('table#listeFiches tbody td:nth-of-type(4)').width(colonne4);
			$('table#listeFiches tbody td:nth-of-type(5)').width(colonne5);
			$('table#listeFiches tbody td:nth-of-type(6)').width(colonne6 - 3);
		});
				
		// On évite le lancement du formulaire
		return false;
	});
	
	
	// Script permettant l'exportation de la sélection actuelle
	$('.exportation').click(function(){
		// On récupère les critères de tri
		var criteres = $('#summaryTri').val();
		
		// On lance le script d'export
		$.get('ajax.php?script=contacts-export', {'tri': criteres})
		 .always(function(){
			$('#exportation-lancee').fadeIn(1000).delay(5000).fadeOut(1000);
		 });
		
		// On annule le clic sur le lien
		return false;
	});
};

$(document).ready(contacts);