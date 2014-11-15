var contacts = function() {
	
	function majListing( action ) {
		// On prépare le tableau des données à envoyer
		var data = [];
		
		// On commence par supprimer un possible bouton d'affichage de la suite pour éviter les interférences
		if (action == 'debut') {
			$('.afficherSuite').remove();
		} else {
			$('.afficherSuite').html('chargement…');
			$('.afficherSuite').attr('disabled', 'disabled');
			$('.afficherSuite').css('background-color', '#E6E5E4');
			$('.afficherSuite').css('cursor', 'default');
			$('.afficherSuite').css('pointer-event', 'none');
		}
		
		// On récupère les données de formulaire
		data["email"] = $('#coordonnees-email').val();
		data["mobile"] = $('#coordonnees-mobile').val();
		data["fixe"] = $('#coordonnees-fixe').val();
		data["electeur"] = $('#coordonnees-electeur').val();
		
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
			debut: nombre
		};
		
		// On effectue l'appel AJAX qui va récupérer les x fiches correspondantes
		$.getJSON('ajax.php?script=contacts-listing', data, function(data) {
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
			var nombreFiches = parseInt(nombre) + 5; console.log(nombreFiches);
			$('#nombreFiches').val(nombreFiches);
			$('.resultatTri').append('<li><button class="afficherSuite clair">Afficher la suite</button></li>');
			
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
};

$(document).ready(contacts);