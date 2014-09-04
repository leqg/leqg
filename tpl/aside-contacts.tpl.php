<aside>
	<?php
	
	// On recherche le module de recherche des fiches contact
		$core->tpl_load('search', 'fiche');
	
	// On recherche le module de recherche des fiches par tags
//		$core->tpl_load('search', 'tags');
		
	// On affiche les boutons de création d'une fiche utilisateur, dossier et actions plate-forme
		$core->tpl_load('menu', 'creation');

	?>
</aside>