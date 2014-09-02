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
				$('main').css('left', '260px');
			} else {
				$('nav#principale').css('left', '-260px');
				$('main').css('left', 0);
			}
			
			// On annule le clique sur le lien pour éviter l'ajout de # à la fin de l'URL
			return false;
		});

		
	// À chaque clique sur un lien du menu principal, on range le menu le temps du chargement
		$('nav#principale a').click(function(){
			$('#menu').toggleClass('actif');
			$('nav#principale').css('left', '-260px');
			$('main').css('left', 0);
		});
	
	
	// Script relatif à l'affichage de la barre des notifications
		$('#notifications').click(function(){
			
			// On annule le clique sur le lien
			return false;
		});
};

$(document).ready(main);