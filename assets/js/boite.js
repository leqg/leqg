var porte = function() {
	// Actions à la création d'une mission
		$('#form-responsable').focus(function(){
			$('#boitage-aside').show();
			$('#boitage-aside ul.formulaire').hide();
			$('#choixResponsable').show();
		});
		
		$('.radioResponsable').click(function(){
			var responsable = $(this).val();
			var nom = $(this).data('nom');
			$('#responsable').val(responsable);
			$('#form-responsable').val(nom);
			
			$('#boitage-aside').hide();
			$('#choixResponsable').hide();
		});
		
	
	// Bouton d'ajout d'une rue à une mission
		$('.ajouterRue').click(function(){
			$('.droite section').fadeOut();
			$('#ajoutRue').fadeIn();
		});
		
	
	// Bouton d'ajout d'une rue à une mission
		$('.ajouterBureau').click(function(){
			$('.droite section').fadeOut();
			$('#ajoutBureau').fadeIn();
		});
	
	// Recherche d'une rue pour l'ajout à une mission
		$('#rechercheRue').keyup(function(){
			var rue = $(this).val();
			
			if (rue.length >= 3) {
				$.getJSON('ajax.php?script=rues', { 'rue' : rue }, function(data) {
					// On commence par vider la liste des résultats avant d'y installer les nouveaux
					$('#listeRues').html('');
					
					$.each( data, function( key, val ) {
					
						// On créé d'abord une nouvelle puce
						$('#listeRues').append('<li id="rue-' + val.rue_id + '"></li>');
						
						// On ajoute après les différents span
						$('#rue-' + val.rue_id).append('<button class="ajouterLaRue" data-rue="' + val.rue_id + '" data-nom="' + val.rue_nom + '">Choisir</button>');
						$('#rue-' + val.rue_id).append('<span class="rue-nom">' + val.rue_nom + '</span>');
						$('#rue-' + val.rue_id).append('<span class="rue-ville">' + val.commune_nom + '</span>');
					});
					
					$('#listeRues').show();
				});
			}
		});
	
	// Recherche d'une rue pour l'ajout à une mission
		$('#rechercheBureau').keyup(function(){
			var bureau = $(this).val();
			
			if (bureau.length >= 2) {
				$.getJSON('ajax.php?script=bureaux', { 'bureau' : bureau }, function(data) {
					// On commence par vider la liste des résultats avant d'y installer les nouveaux
					$('#listeBureaux').html('');
					
					$.each( data, function( key, val ) {
					
						// On créé d'abord une nouvelle puce
						$('#listeBureaux').append('<li id="bureau-' + val.bureau_id + '"></li>');
						
						// On ajoute après les différents span
						$('#bureau-' + val.bureau_id).append('<button class="ajouterLeBureau" data-bureau="' + val.bureau_id + '" data-numero="' + val.bureau_numero + '" data-nom="' + val.bureau_nom + '">Ajouter</button>');
						$('#bureau-' + val.bureau_id).append('<span class="bureau-nom">Bureau ' + val.bureau_numero + ' ' + val.bureau_nom + '</span>');
						$('#bureau-' + val.bureau_id).append('<span class="bureau-ville">' + val.commune_nom + '</span>');
					});
					
					$('#listeBureaux').show();
				});
			}
		});
	
	// Script d'ajout d'une rue à la mission
		$('#ajoutRue').on('click', '.ajouterLaRue', function(){
			var rue = $(this).data('rue');
			var rueNom = $(this).data('nom');
			
			$('#rueEntiere').data('rue', rue);
			$('#rueSelectionImmeuble').val(rueNom);
			
			$('#ajoutRue').fadeOut();
			$('#choixImmeuble').fadeIn();
		});
	
	// Script d'ajout d'un bureau à la mission
		$('#ajoutBureau').on('click', '.ajouterLeBureau', function(){
			var bureau = $(this).data('bureau');
			var bureauNumero = $(this).data('numero');
			var bureauNom = $(this).data('nom');
			var bureauVille = $(this).data('ville');
			var mission = $('#titre-mission').data('mission');
	
			$.post('ajax.php?script=boitage-ajout-bureau', { bureau: bureau, mission: mission }, function(){
				var destination = 'index.php?page=boite&mission=' + mission;
				$(location).attr('href', destination);
			});
		});
		
	// Script qui permet de revenir à la recherche d'une rue au clic sur le nom de la rue
		$('#rueSelectionImmeuble').click(function(){
			$('#choixImmeuble').fadeOut();
			$('#ajoutRue').fadeIn();
			$('#rechercheRue').focus();
		});
	
	
	// Script permettant d'ajouter une rue entière à une mission
		$('#rueEntiere').click(function(){
			var rue = $(this).data('rue');
			var mission = $(this).data('mission');
						
			$.get('ajax.php?script=mission-ajout-rue', { rue: rue, mission: mission }, function(){
				var destination = 'index.php?page=boite&mission=' + mission;
				$(location).attr('href', destination);
			});
		});
	
	
	// Script permettant de voir les immeubles concernés au sein d'une rue
		$('#listeDesRues').on('click', '.voirRue', function(){
			if ($(this).html() == 'Consulter') {
				$('.droite section').fadeOut();
				$('.voirRue').hide();
	
				var mission = $('#titre-mission').data('mission');
				var nom = $(this).data('nom');
				var rue = $(this).data('rue');
				
				// Si un bouton de reporting de la rue existe déjà, on le supprime
				$('a.reporting-rue').remove();
				
				// On met en place le nom de la rue
				$('#listeImmeublesParRue .nomRue span').html(nom);
				$('#listeImmeublesParRue ul.form-liste').html('');
				
				// On recherche la liste des immeubles au sein de cette rue
				$.getJSON('ajax.php?script=boitage-liste-immeubles', { rue: rue, mission: mission }, function(data){
					$.each( data , function( key , val ) {
						// On commence par créer ce nouvel élement de liste
						$('#listeImmeublesParRue ul.form-liste').append('<li class="detailImmeuble" id="detailImmeuble-' + val + '"><span>' + val + '</span> ' + nom + '</li>');
					});
				});
				
				// On rajoute l'accès à la feuille de reporting
				$('#listeImmeublesParRue ul.form-liste').after('<a class="reporting-rue nostyle" href="index.php?page=boite&rue=' + rue + '&mission=' + mission + '"><button>Voir la mission dans cette rue</button></a>');
				
				// On affiche le bloc
				$('#listeImmeublesParRue').fadeIn();
				$(this).show();
				$(this).html('Fermer');
			} else {
				$('.droite section').fadeOut();
				$('#boitage-statistiques').fadeIn();
				$(this).html('Consulter');
				$('.voirRue').show();
			}
		});
	
	
	// On cache tout ce qui doit être caché au premier abord
		$('#liste-ville').hide();
		$('#liste-rue').hide();
		
	// On décoche toutes les sélections d'immeuble au démarrage
		$('.checkImmeuble').attr('checked', false);
	
	// Script de recherche de la ville d'une mission
		$('#recherche-ville').keyup(function(){
			var ville = $(this).val();
			
			if (ville.length >= 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=boite-recherche-ville',
					data: { 'ville': ville },
					dataType: 'html'
				}).done(function(data){
					$('#resultats-ville').html(data);
					$('#liste-ville').show();
				});
			} else {
				$('#liste-ville').hide();
			}
		});
	
	// Script de recherche de la rue d'une mission
		$('#recherche-rue').keyup(function(){
			var rue = $(this).val();
			var ville = $(this).data('ville');
			
			if (rue.length >= 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?script=boite-recherche-rue',
					data: { 'ville': ville, 'rue': rue },
					dataType: 'html'
				}).done(function(data){
					$('#resultats-rue').html(data);
					$('#liste-rue').show();
				});
			} else {
				$('#liste-rue').hide();
			}
		});
	
	// Script de sélection des immeubles
		$('.checkImmeuble').change(function(){
			var value = $(this).val();
			
			$('#labelImmeuble-' + value).toggleClass('choisi');
		});
		
		$('#toutSelect').click(function(){
			$('.checkImmeuble').attr('checked', 'checked');
		});

    
    // Action de reporting
    $('.bouton-reporting').on('click', 'label', function() {
        var immeuble = $(this).data('immeuble');
        var statut = $(this).data('val');
        var mission = $('.titre').data('mission');
        
        $.get('ajax.php?script=boitage-reporting-web', { mission: mission, immeuble: immeuble, statut: statut }, function() {
            $('tr.ligne-immeuble-' + immeuble).remove();
        });
    });
    
    
    // Action d'inscription
    $('.inscription').click(function() {
	    var mission = $(this).data('mission');
	    
	    // On lance l'inscription
	    $.post('ajax.php?script=boite-inscription', { mission: mission }, function() {
			var destination = 'index.php?page=boite&action=missions';
			$(location).attr('href', destination);
	    });
    });
	
	
	// Action de suppression de la mission
	$('.supprimerMission').click(function() {
	    // On vérifie cette suppression
	    if (confirm('Voulez-vous vraiment supprimer cette mission ? Cette action est irréversible.'))
	    {
    	    var fiche = $('.titre').data('mission');
    	    $.get('ajax.php?script=mission-suppression', { mission: fiche }, function() {
        	    var url = 'index.php?page=boite';
        	    document.location.href = url;
    	    });
    	    return false;
	    }
	    else
	    {
    	    return false;
	    }
	});
	
};

$(document).ready(porte);