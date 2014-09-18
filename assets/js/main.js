/*
	Fichier des fonctions générales et initiales Javascript et jQuery du site
*/

function getURLVar(variable)
{
   var query = window.location.search.substring(1);
   var vars = query.split("&");
   for (var i=0;i<vars.length;i++) {
           var pair = vars[i].split("=");
           if(pair[0] == variable){return pair[1];}
   }
   return(false);
}


var main = function() {

	// Fonctionnement des overlays
		$('.ouvertureOverlay').click(function(){
			var overlay = $(this).data('overlay');
			$("#" + overlay).fadeIn();
			return false;
		});
		
		$('.fermetureOverlay').click(function(){
			var overlay = $(this).data('overlay');
			$("#" + overlay).fadeOut();
			return false;
		});


	// On fait que les formulaires d'upload d'un fichier s'affichent mieux
		$("#form-fichier").change(function(){
			var value = $(this).val();
			$(".upload-file").html(value);
		});
		
		$(".bouton-upload").click(function(){ $("#form-fichier").click(); });
	
	
	// Script relatif au menu principal
		$('#menu').click(function(){
			// On ajout ou non la classe actif à l'icône du menu pour commencer
			$(this).toggleClass('actif');
			
			if ($(this).hasClass('actif')) {
				$('nav#principale').css('left', 0);
				$('main').css('left', '165px');
			} else {
				$('nav#principale').css('left', '-165px');
				$('main').css('left', 0);
			}
			
			// On annule le clique sur le lien pour éviter l'ajout de # à la fin de l'URL
			return false;
		});

		
	// À chaque clique sur un lien du menu principal ou sur un lien de la page en général, on range le menu le temps du chargement
		$('nav#principale a').click(function(){
			if ($('#menu').hasClass('actif')) {
				$('#menu').toggleClass('actif');
				$('nav#principale').css('left', '-165px');
				$('main').css('left', 0);
			}
		});
	
	
	// Script relatif à l'affichage de la barre des notifications
		$('#notifications').click(function(){
			
			// On annule le clique sur le lien
			return false;
		});
		
	
	// Script relatif à la recherche rapide de fiche
		$('#rechercheRapide').click(function(){
			if ($(this).hasClass('fermer')) {
				// On affiche d'abord le formulaire
				$('#searchSubmit').css({
					'opacity': 0,
					'pointer-events': 'none'
				});
				
				$('#searchForm').css({
					'opacity': 0,
					'pointer-events': 'none',
					'width': '0'
				});
				
				// On transforme la croix en loupe
				$(this).removeClass('fermer');
				
				// On annule le clique sur le lien pour éviter le # en fin d'URL
				return false;
			} else {
				// On affiche d'abord le formulaire
				$('#searchForm').css({
					'opacity': 1,
					'pointer-events': 'all',
					'width': '400px'
				});
				
				$('#searchSubmit').css({
					'opacity': 1,
					'pointer-events': 'all'
				});
				
				// On transforme la loupe en croix
				$(this).addClass('fermer');
				
				// On déplace le pointeur dans le formulaire ouvert
				$('#searchForm').focus();
				
				// On annule le clique sur le lien pour éviter le # en fin d'URL
				return false;
			}
		});
		
		
	// Script d'ouverture du centre de notification
		$('#notifications').click(function(){
			if ($(this).hasClass('actif')) {
				$(this).toggleClass('actif');
				$('#notificationCenter').css('right', '-350px');
			} else {
				$(this).toggleClass('actif');
				$('#notifications').html('');
				$('#notificationCenter').css('right', 0);
			}
		});
		
};

$(document).ready(main);