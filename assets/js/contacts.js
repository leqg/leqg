var contacts = function() {
	// Pour le tableau des fiches, on récupère les largeurs pour les affecter à l'entête
	var colonne1 = $('table#listeFiches tbody td:nth-of-type(1)').width();
	var colonne2 = $('table#listeFiches tbody td:nth-of-type(2)').width();
	var colonne3 = $('table#listeFiches tbody td:nth-of-type(3)').width();
	var colonne4 = $('table#listeFiches tbody td:nth-of-type(4)').width();
	var colonne5 = $('table#listeFiches tbody td:nth-of-type(5)').width();
	var colonne6 = $('table#listeFiches tbody td:nth-of-type(6)').width();
	$('table#listeFiches thead th:nth-of-type(1)').width(colonne1);
	$('table#listeFiches thead th:nth-of-type(2)').width(colonne2 - 3);
	$('table#listeFiches thead th:nth-of-type(3)').width(colonne3 - 4);
	$('table#listeFiches thead th:nth-of-type(4)').width(colonne4 - 4);
	$('table#listeFiches thead th:nth-of-type(5)').width(colonne5 - 4);
	$('table#listeFiches thead th:nth-of-type(6)').width(colonne6 - 3);
};

$(document).ready(contacts);