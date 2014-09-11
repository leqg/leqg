var administration = function() {
	$('#ajoutCompte').click(function(){
		$('#creationCompte').fadeIn();
		return false;
	});
	
	$('.modifier').click(function(){
		var id = $(this).data('user');
	
		// On récupère les informations sur la fiche demandée
		var params = { script: 'user-infos', user: id };
		$.getJSON('ajax.php', params, function(data){
			$('#form-modifier-compte').val(data.id);
			$('#form-modifier-firstname').val(data.firstname);
			$('#form-modifier-lastname').val(data.lastname);
			$('#form-modifier-email').val(data.email);
			$('label[for="modifier-auth-' + data.auth + '"]').click();
			
			$('#modifierCompte').fadeIn();
			
			return false;
		});
		
		return false;
	});
	
	$('#formModificationCompte').submit(function(){
		var valeurs = $(this).serialize();

		$.ajax({
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: valeurs,
			dataType: 'json'
		}).done(function(data){
			$('#user-' + data.compte + ' .nom').html(data.firstname + ' ' + data.lastname);
			$('#user-' + data.compte + ' .role').html(data.auth);
			$('#user-' + data.compte + ' .ouvrirMenu').toggleClass('actif');

			$('.overlayForm').fadeOut();
		});
	
		// code pour rester sur la même page
		return false;
	});
	
	$('.fermetureOverlay').click(function(){
		$('.overlayForm').fadeOut();
		return false;
	});
	
	$('.ouvrirMenu').click(function(){
		$(this).toggleClass('actif');
		
		return false;
	});
};

$(document).ready(administration);