var contacts = function() {
	
	function majListing() {
		// On prépare le tableau des données à envoyer
		var data = [];
		
		// On récupère les données de formulaire
		data["email"] = $('#coordonnees-email').val();
		data["mobile"] = $('#coordonnees-mobile').val();
		data["fixe"] = $('#coordonnees-fixe').val();
		data["electeur"] = $('#coordonnees-electeur').val();
		
		// Nombre de fiches déjà affichées
		var nombre = $('#nombreFiches').val();
		
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
			// On commence par vider la liste des résultats affichés
			$('.resultatTri').html('');
			
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
				$('.resultatTri .contact-' + val.contact_id + ' li strong').html(val.nom_affichage);
				$('.resultatTri .contact-' + val.contact_id + ' li p .age').html(val.age);
				$('.resultatTri .contact-' + val.contact_id + ' li p .ville').html(val.ville);
			});
		});
		
		return true;
	};
	
	$('.selectionTri').change(majListing);
};

$(document).ready(contacts);