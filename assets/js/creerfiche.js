var creerfiche = function() {
	$("#nouvelleFiche").click(function(){
		var nom = $("#selectionDoublons").data('nom');
		var nomUsage = $("#selectionDoublons").data('nomUsage');
		var prenom = $("#selectionDoublons").data('prenom');
		var sexe = $("#selectionDoublons").data('sexe');
		var fixe = $("#selectionDoublons").data('fixe');
		var mobile = $("#selectionDoublons").data('mobile');
		var dateNaissance = $("#selectionDoublons").data('naissance');
		var email = $("#selectionDoublons").data('email');
		
		$.ajax({
			type: 'POST',
			url: 'ajax.php?script=creer-fiche',
			data: { 'nom': nom, 'nomUsage': nomUsage, 'prenom': prenom, 'sexe': sexe, 'fixe': fixe, 'mobile': mobile, 'email': email, 'dateNaissance': dateNaissance },
			dataType: 'html'
		}).done(function(fiche){
			var destination = 'index.php?page=fiche&id=' + fiche + '&modifierAdresse=true';
			$(location).attr('href', destination);
		}).error(function(){
			var destination = 'index.php?page=fiche&action=creation';
			$(location).attr('href', destination);
		});
	});
	
	$(".existante").click(function() {
		var contact = $(this).data('contact');
		var nom = $("#selectionDoublons").data('nom');
		var nomUsage = $("#selectionDoublons").data('nomUsage');
		var prenom = $("#selectionDoublons").data('prenom');
		var sexe = $("#selectionDoublons").data('sexe');
		var fixe = $("#selectionDoublons").data('fixe');
		var mobile = $("#selectionDoublons").data('mobile');
		var dateNaissance = $("#selectionDoublons").data('naissance');
		var email = $("#selectionDoublons").data('email');
		
		$.ajax({
			type: 'POST',
			url: 'ajax.php?script=fusion-donnees-nouvelle-fiche',
			data: { 'contact': contact, 'fixe': fixe, 'mobile': mobile, 'email': email, 'dateNaissance': dateNaissance },
			dataType: 'html'
		}).done(function(data){
			//console.log(data);
			var destination = 'index.php?page=fiche&id=' + contact;
			$(location).attr('href', destination);
		}).error(function(){
			var destination = 'index.php?page=fiche&action=creation';
			$(location).attr('href', destination);
		});
	});
}

$(document).ready(creerfiche);