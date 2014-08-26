var dossier = function() {
	// On cache tous les div dans aside
	$('aside div').hide();
	$('#ajoutFichier').show();
	
	// On regarde quel div afficher
	if (getURLVar('modifierInfos')) {
		$('#modifierInfos').show();
	} else {
		$('#historique').show();
	}
};

$(document).ready(dossier);