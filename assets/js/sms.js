var sms = function() {
	
	// On récupère la liste des contacts au chargement de la page 
	function liste( ) {
		var campagne = $('.titreCampagne').data('campagne');
		
		// On récupère la liste des contacts
		$.getJSON('ajax.php?script=campagne-liste', { campagne: campagne }, function(data) {
			// On vide la liste
			$('.listeContacts').html('');
			
			// On fait une boucle des informations
			$.each(data, function(key, val) {
				// On ajoute une puce
				var sexe;
				if (val.contact_sexe = 'M') { sexe = 'homme'; } else if (val.contact_sexe = 'F') { sexe = 'femme'; } else { sexe = 'isexe'; }
				$('.listeContacts').append('<a href="" class="nostyle contact-' + val.contact_md5 + '"><li class="contact ' + sexe + '"><strong></strong><p><span class="ville"></span></p></li></a>');
				
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
			
				// On rempli les informations
				$('.listeContacts a.contact-' + val.contact_md5).attr('href', 'index.php?page=contact&contact=' + val.contact_md5);
				$('.listeContacts a.contact-' + val.contact_md5 + ' li strong').html(nom);
				$('.listeContacts a.contact-' + val.contact_md5 + ' li p .ville').html(val.ville);
			});
		});
	}
	
	if ($('.titreCampagne').data('page') == 'campagne') { liste(); }
	
};

$(document).ready(sms);