var installation = function() {
		
	// On lance au click la première partie de l'installation, avec la base de données des régions
	$("#demarrage").click(function(){
		// On prévient qu'on construit la base de données
		$("#install").html('Création de la base de données<br>');
		
		$.ajax({
			url: 'ajax-admin.php?script=installation-bdd',
			dataType: 'html'
		}).done(function(){
			// on affiche un message pour expliquer que la création est finie
			$("#install").append('Création de la base de données réussie<br><br>');
	
			// Tout d'abord, on affiche le message d'attente
			$("#install").append('Installation des régions en cours…<br>');
			
			$.ajax({
				url: 'ajax-admin.php?script=installation-regions',
				dataType: 'html'
			}).done(function(){
				// on affiche un message pour expliquer que l'installation est finie
				$("#install").append('Installation des régions terminée<br><br>');
				
				// On affichage un message expliquant qu'on commence l'installation des départements
				$("#install").append('Installation des départements en cours…<br>');
				
				// On lance l'installation des départements		
				$.ajax({
					url: 'ajax-admin.php?script=installation-departements',
					dataType: 'html'
				}).done(function(){
				
					// on affiche un message pour expliquer que l'installation est finie
					$("#install").append('Installation des départements terminée<br><br>');
					
					// on affichage un message expliquant qu'on commence l'installation des arrondissements
					$("#install").append('Installation des arrondissements en cours…<br>');
	
					// On lance l'installation des arrondissements		
					$.ajax({
						url: 'ajax-admin.php?script=installation-arrondissements',
						dataType: 'html'
					}).done(function(){
					
						// on affiche un message pour expliquer que l'installation est finie
						$("#install").append('Installation des arrondissements terminée<br><br>');
						
						// on affichage un message expliquant qu'on commence l'installation des arrondissements
						$("#install").append('Installation des cantons en cours…<br>');
						
						// On lance l'installation des cantons
						$.ajax({
							url: 'ajax-admin.php?script=installation-cantons',
							dataType: 'html'
						}).done(function(){
						
							// on affiche un message pour expliquer que l'installation est finie
							$("#install").append('Installation des cantons terminée<br><br>');
							
							// on affichage un message expliquant qu'on commence l'installation des communes
							$("#install").append('Installation des communes en cours…<br>');
			
							// On lance l'installation des communes		
							$.ajax({
								url: 'ajax-admin.php?script=installation-communes',
								dataType: 'html'
							}).done(function(){
							
								// on affiche un message pour expliquer que l'installation est finie
								$("#install").append('Installation des communes terminée<br><br>');
								
								// on affichage un message expliquant qu'on commence l'installation des codes postaux
								$("#install").append('Installation des codes postaux en cours…<br>');
								
								// on lance l'installation des codes postaux
								$.ajax({
									url: 'ajax-admin.php?script=installation-zipcodes',
									dataType: 'html'
								}).done(function(){
	
									// on affiche un message pour expliquer que l'installation est finie
									$("#install").append('Installation des codes postaux terminée<br><br>FIN DES INSTALLATIONS.');
									
								}).error(function(){
									$("#install").html('Installation des codes postaux en erreur, contactez l\'équipe technique');
								});
				
							// On lance l'installation des communes		
							}).error(function(){
								$("#install").html('Installation des communes en erreur, contactez l\'équipe technique');
							});
							
						}).error(function(){
							$("#install").html('Installation des cantons en erreur, contactez l\'équipe technique');
						});
	
					}).error(function(){
						$("#install").html('Installation des arrondissements en erreur, contactez l\'équipe technique');
					});
				}).error(function(){
					$("#install").html('Installation des départements en erreur, contactez l\'équipe technique');
				});
				
			}).error(function(){
				$("#install").html('Installation des régions en erreur, contactez l\'équipe technique');
			});

		}).error(function(){
			$("#install").html('Création de la base de données en erreur, contactez l\'équipe technique');
		});
	});
	
}

$(document).ready(installation);