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
};

$(document).ready(main);