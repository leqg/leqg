var publi = function() {
	
	// On récupère la liste des contacts au chargement de la page 
	function liste( ) {
		var campagne = $('.titreCampagne').data('campagne');
		
		// On récupère la liste des contacts
		$.getJSON('ajax.php?script=campagne-liste', { campagne: campagne }, function(data) {
			// On vide la liste
			$('.listeContacts').html('');
			
			// On fait une boucle des informations
			$.each(data, function(key, val) {
				// On créé la puce
				$('.listeContacts').append('<a href="" class="nostyle contact-' + val.people + '"><li class="contact"><strong></strong><p><span class="ville"></span></p></li></a>');
				
				// On y applique un sexe
				if (val.sexe == 'M') {
					$('.listeContacts a.contact-' + val.people + ' li').addClass('homme');
				} 
				else if (val.sexe == 'F') { 
					$('.listeContacts a.contact-' + val.people + ' li').addClass('femme');
				} 
				else { 
					$('.listeContacts a.contact-' + val.people + ' li').addClass('isexe');
				}
				
				// On ajoute demande le nom de la fiche
				var nom;
				if (val.nom.length == 0) {
					if (val.contact_organisme.length == 0) {
						nom = 'Contact sans nom';
					} else {
						nom = val.organisme;
					}
				} else {
					nom = val.nom + ' ' + val.nom_usage + ' ' + val.prenoms;
				}
			
				// On rempli les informations
				$('.listeContacts a.contact-' + val.people).attr('href', 'index.php?page=contact&contact=' + val.people);
				$('.listeContacts a.contact-' + val.people + ' li strong').html(nom);
				$('.listeContacts a.contact-' + val.people + ' li p .ville').html(val.ville);
			});
		});
	}
	
	if ($('.titreCampagne').data('page') == 'campagne') { liste(); }
	
};

$(document).ready(publi);