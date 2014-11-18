var contacts = function() {
	
	function majListing( action ) {
		// On prépare le tableau des données à envoyer
		var data = [];
		
		// On commence par supprimer un possible bouton d'affichage de la suite pour éviter les interférences
		$('.afficherSuite').html('chargement…');
		$('.afficherSuite').attr('disabled', 'disabled');
		$('.afficherSuite').css('background-color', '#E6E5E4');
		$('.afficherSuite').css('cursor', 'default');
		$('.afficherSuite').css('pointer-event', 'none');
		
		// On récupère les données de formulaire
		data["email"] = $('#coordonnees-email').val();
		data["mobile"] = $('#coordonnees-mobile').val();
		data["fixe"] = $('#coordonnees-fixe').val();
		data["electeur"] = $('#coordonnees-electeur').val();
		data["criteres"] = $('#listeCriteresTri').val();
		
		// Nombre de fiches déjà affichée
		if (action == 'debut')
		{
			var nombre = 0;
			$('#nombreFiches').val(0);
		}
		else
		{
			var nombre = $('#nombreFiches').val();
		}
		
		// On prépare les données qui vont être envoyées
		var data = {
			email: data["email"],
			mobile: data["mobile"],
			fixe: data["fixe"],
			electeur: data["electeur"],
			criteres: data["criteres"],
			debut: nombre
		};
		
		// On effectue l'appel AJAX qui va récupérer les x fiches correspondantes
		$.getJSON('ajax.php?script=contacts-listing', data, function(data) {
			// On vérifie si ce n'est pas la fin de la liste (suite demandée et aucune donnée retournée
			if (data == '' && action == 'suite') {
				// On supprime maintenant le bouton
				$('.afficherSuite').html('fin de la liste');
				$('.afficherSuite').attr('disabled', 'disabled');
				$('.afficherSuite').css('background-color', '#E6E5E4');
				$('.afficherSuite').css('color', '#FFFFFF');
				$('.afficherSuite').css('cursor', 'default');
				$('.afficherSuite').css('pointer-event', 'none');
			}
			
			// Sinon on traite ces nouvelles données
			else {
				if (action == 'debut')
				{
					// On commence par vider la liste des résultats affichés
					$('.resultatTri').html('');	
				}
				else
				{
					// On supprime maintenant le bouton
					$('.afficherSuite').remove();
				}
				
				// On retire de l'affichage toutes les sections ouvertes à droite, pour afficher uniquement celle qui nous intéresse
				$('.droite section').hide();
				$('.droite .actionsFiches').fadeIn();
				$('.droite .listeFiches').fadeIn();
				
				// On va faire une boucle de toutes les fiches créées pour les afficher dans cette liste de contacts
				$.each(data, function(key, val){
					// on détermine le sexe à afficher
					if (val.contact_sexe == 'M') {
						var sexe = 'homme';
					}
					else if (val.contact_sexe == 'F') {
						var sexe = 'femme';
					}
					else {
						var sexe = 'isexe';
					}
					
					$('.resultatTri').append('<a href="index.php?page=contact&contact=' + val.contact_md5 + '" class="nostyle contact-' + val.contact_id + '"><li class="contact ' + sexe + '"><strong></strong><p><span class="age"></span> - <span class="ville"></span></p></li></a>');
					
					// On ajoute demande le nom de la fiche
					var nom;
					if (val.nom_affichage.length == 0) {
						if (val.contact_organisme.length == 0) {
							nom = 'Contact sans nom';
						} else {
							nom = val.contact_organisme;
						}
					} else {
						nom = val.nom_affichage;
					}
					
					// On affecte les données aux balises HTML
					$('.resultatTri .contact-' + val.contact_id + ' li strong').html(nom);
					$('.resultatTri .contact-' + val.contact_id + ' li p .age').html(val.age);
					$('.resultatTri .contact-' + val.contact_id + ' li p .ville').html(val.ville);
				});
				
				// On ajoute le bouton permettant d'afficher les 5 fiches suivantes
				var nombreFiches = parseInt(nombre) + 5;
				$('#nombreFiches').val(nombreFiches);
				$('.resultatTri').append('<li><button class="afficherSuite clair">Afficher la suite</button></li>');
			}
		});
		
		return true;
	};
	
	$('.selectionTri').change(function() { 
		// On met à zéro le nombre de fiches déjà affichées
		$('#nombreFiches').val(0);
		
		// On met à jour le listing
		majListing('debut'); 
	});
	
	$('.resultatTri').on('click', '.afficherSuite', function() {
		majListing('suite');
	});
	
	
	// Script de fermeture de la colonne de droite
	$('.fermerColonne').click(function() {
		$('.droite section').hide();
		$('.droite section:not(.invisible)').fadeIn();		
		
		// On vide certains formulaires automatiquement
		$('#choixCritereThema').val('');
		$('#rechercheBureauVote').val('');
		$('.listeDesBureaux').html('');
		
		return false;
	});
	
	
	// Affichage du formulaire de sélection d'un critère de tri
	$('.ajoutTri').click(function() {
		// On récupère le type de critère choisi
		var type = $(this).data('critere');
		
		// On cache la colonne de droite pour afficher le formulaire
		$('.droite section').hide();
		$('.selectionCritere-' + type).fadeIn();
	});
	
	
	// Script de recherche des bureaux de vote
	$('#rechercheBureauVote').keyup(function() {
		// On récupère les informations tapées
		var recherche = $(this).val();
		
		// On effectue une recherche AJAX pour trouver le bureau
		$.getJSON('ajax.php?script=bureaux', { bureau: recherche }, function(data) {
			// On vide la liste des bureaux de vote affichés précédemment
			$('.listeDesBureaux').html('');
			
			// On fait la boucle de tous les éléments trouvés et on les affiche
			$.each(data, function(key, val) {
				// Pour chaque bureau, on créé une puce
				$('.listeDesBureaux').append('<li class="bureau-' + val.bureau_id + '"><span class="bureau-nom"></span><span class="bureau-ville"></span><button class="choisirBureau" data-bureau="' + val.bureau_id + '" data-numero="' + val.bureau_numero + '">Choisir</button></li>');
				$('.bureau-' + val.bureau_id + ' .bureau-nom').html('Bureau ' + val.bureau_numero + ' ' + val.bureau_nom);
				$('.bureau-' + val.bureau_id + ' .bureau-ville').html(val.commune_nom + ' (' + val.departement_id + ')');
			});
		});
	});
	
	
	// Script de recherche des rues
	$('#rechercheRue').keyup(function() {
		// On récupère les informations tapées
		var recherche = $(this).val();
		
		// S'il y a plus de trois caractères entrés
		if (recherche.length >= 3)
		{
			// On effectue une recherche AJAX pour trouver la rue
			$.getJSON('ajax.php?script=rues', { rue: recherche }, function(data) {
				// On vide la liste des rues affichés précédemment
				$('.listeDesRues').html('');
				
				// On fait la boucle de tous les éléments trouvés et on les affiche
				$.each(data, function(key, val) {
					// Pour chaque rue, on créé une puce
					$('.listeDesRues').append('<li class="rue-' + val.rue_id + '"><span class="rue-nom"></span><span class="rue-ville"></span><button class="choisirRue" data-rue="' + val.rue_id + '" data-nom="' + val.rue_nom + '">Choisir</button></li>');
					$('.rue-' + val.rue_id + ' .rue-nom').html(val.rue_nom);
					$('.rue-' + val.rue_id + ' .rue-ville').html(val.commune_nom + ' (' + val.departement_id + ')');
				});
			});
		} else {
			$('.listeDesRues').html('');
		}
	});
	
	
	// Script de recherche des rues
	$('#rechercheVille').keyup(function() {
		// On récupère les informations tapées
		var recherche = $(this).val();
		
		// S'il y a plus de trois caractères entrés
		if (recherche.length >= 3)
		{
			// On effectue une recherche AJAX pour trouver la rue
			$.getJSON('ajax.php?script=villes', { ville: recherche }, function(data) {
				// On vide la liste des rues affichés précédemment
				$('.listeDesVilles').html('');
				
				// On fait la boucle de tous les éléments trouvés et on les affiche
				$.each(data, function(key, val) {
					// Pour chaque rue, on créé une puce
					$('.listeDesVilles').append('<li class="ville-' + val.commune_id + '"><span class="ville-nom"></span><span class="ville-dept"></span><button class="choisirVille" data-ville="' + val.commune_id + '" data-nom="' + val.commune_nom + '">Choisir</button></li>');
					$('.ville-' + val.commune_id + ' .ville-nom').html(val.commune_nom);
					$('.ville-' + val.commune_id + ' .ville-dept').html(val.departement_nom);
				});
			});
		} else {
			$('.listeDesVilles').html('');
		}
	});
	
	
	// Script à la validation d'un nouveau bureau
	$('.listeDesBureaux').on('click', '.choisirBureau', function() {
		// On récupère le critère demandé
		var bureau = $(this).data('bureau');
		var numero = $(this).data('numero');
		
		// On efface le formulaire et la liste des résultats
		$('#rechercheBureauVote').val('');
		$('.listeDesBureaux').html('');
		
		// On ajoute ce critère à la liste des critères
		var criteres = $('#listeCriteresTri').val();
		var newCriteres = criteres + 'bureau:' + bureau + ';';
		
		// On met à jour la liste des critères
		$('#listeCriteresTri').val(newCriteres);
		
		// On ajoute le critère à la liste dans la colonne de gauche
		$('.premierAjoutTri').before('<li class="tri bureau" data-critere="bureau" data-valeur="' + bureau + '">Bureau ' + numero + '</li>');
		
		// On met à zéro le nombre de fiches déjà affichées
		$('#nombreFiches').val(0);
		
		// On met à jour le listing
		majListing('debut');
	});
	
	
	// Script à la validation d'une nouvelle rue
	$('.listeDesRues').on('click', '.choisirRue', function() {
		// On récupère le critère demandé
		var rue = $(this).data('rue');
		var nom = $(this).data('nom');
		
		// On efface le formulaire et la liste des résultats
		$('#rechercheRue').val('');
		$('.listeDesRues').html('');
		
		// On ajoute ce critère à la liste des critères
		var criteres = $('#listeCriteresTri').val();
		var newCriteres = criteres + 'rue:' + rue + ';';
		
		// On met à jour la liste des critères
		$('#listeCriteresTri').val(newCriteres);
		
		// On ajoute le critère à la liste dans la colonne de gauche
		$('.premierAjoutTri').before('<li class="tri rue" data-critere="rue" data-valeur="' + rue + '">' + nom + '</li>');
		
		// On met à zéro le nombre de fiches déjà affichées
		$('#nombreFiches').val(0);
		
		// On met à jour le listing
		majListing('debut');
	});
	
	
	// Script à la validation d'une nouvelle rue
	$('.listeDesVilles').on('click', '.choisirVille', function() {
		// On récupère le critère demandé
		var ville = $(this).data('ville');
		var nom = $(this).data('nom');
		
		// On efface le formulaire et la liste des résultats
		$('#rechercheVille').val('');
		$('.listeDesVilles').html('');
		
		// On ajoute ce critère à la liste des critères
		var criteres = $('#listeCriteresTri').val();
		var newCriteres = criteres + 'ville:' + ville + ';';
		
		// On met à jour la liste des critères
		$('#listeCriteresTri').val(newCriteres);
		
		// On ajoute le critère à la liste dans la colonne de gauche
		$('.premierAjoutTri').before('<li class="tri ville" data-critere="ville" data-valeur="' + ville + '">' + nom + '</li>');
		
		// On met à zéro le nombre de fiches déjà affichées
		$('#nombreFiches').val(0);
		
		// On met à jour le listing
		majListing('debut');
	});
	
	
	// Script à la validation d'un nouveau critère thématique
	$('.validerChoixCritereThema').click(function() {
		// On récupère le critère demandé
		var thema = $('#choixCritereThema').val();
		
		// On efface ce formulaire
		$('#choixCritereThema').val('');
		
		// On ajoute ce critère à la liste des critères
		var criteres = $('#listeCriteresTri').val();
		var newCriteres = criteres + 'thema:' + thema + ';';
		
		// On met à jour la liste des critères
		$('#listeCriteresTri').val(newCriteres);
		
		// On ajoute le critère à la liste dans la colonne de gauche
		$('.premierAjoutTri').before('<li class="tri thema" data-critere="thema" data-valeur="' + thema + '">' + thema + '</li>');
		
		// On met à zéro le nombre de fiches déjà affichées
		$('#nombreFiches').val(0);
		
		// On met à jour le listing
		majListing('debut');
	});
	
	
	// Script de suppression d'un critère
	$('.listeTris').on('dblclick', '.tri:not(.ajoutTri)', function() {
		// On récupère le type
		var type = $(this).data('critere');
		var valeur = $(this).data('valeur');
		var chaine = ';' + type + ':' + valeur + ';';
		
		// On récupère les critères
		var criteres = ';' + $('#listeCriteresTri').val();
		
		// On supprime des critères le critère demandé
		criteres = criteres.replace(chaine, ';');
		
		// On met à jour les critères
		$('#listeCriteresTri').val(criteres);

		// On fini par supprimer cette puce
		$(this).remove();
		
		// On met à zéro le nombre de fiches déjà affichées
		$('#nombreFiches').val(0);
		
		// On met à jour le listing
		majListing('debut');
	});
};

$(document).ready(contacts);