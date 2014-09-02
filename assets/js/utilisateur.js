var utilisateur = function() {

	function couleurVide() {
		if ($("#form-phone").val() == '') { $("#form-phone").addClass('vide'); } else { $("#form-phone").removeClass('vide'); }
	}
	
	// fonction lancée au démarrage de la page
	$("#form-phone").ready(couleurVide);
	
	// fonction lancée à chaque modification
	$("#form-phone").change(couleurVide);
	
				
			
	// Quand les champs de formulaire sont sélectionnés, on affiche le bouton valider
		$("#form-phone").focus(function(){
			$("#valider-form-phone").fadeIn('slow');
		});


	// Simplement quand on sort du formulaire, on supprime le bouton valider
		$("#form-phone").blur(function(){
			$("#valider-form-phone").fadeOut('slow');
		});
				
				
	$("#form-phone").change(function() {
		// On récupère les informations
		var value = $(this).val();
		var user = $(this).data('user');
		
		// On met en place un affichage d'attente
		$("#valider-form-phone").fadeOut('slow');
		$("#sauvegarde-form-phone").fadeIn('slow');
	
		// On appelle l'AJAX
		$.ajax({
			type: 'POST',
			url: 'ajax-form.php?action=utilisateur-nouveau-telephone',
			data: { 'user': user, 'valeur': value },
		}).done(function(){
			$("#sauvegarde-form-phone").fadeOut('slow');
			$("#reussite-form-phone").fadeIn('slow').delay(1500).fadeOut('slow');
		});
	});
};

$(document).ready(utilisateur);