<?php
	// On importe d'abord les fichiers associés
	require_once 'includes.php';
	
	// On importe le header
	$core->tpl_header();

	// On fait la liste des fichiers présents dans le répertoire csv/
	if ($dossier = opendir('./csv')) :
	
		// On vérifie que l'ouverture et la lecture du dossier n'a pas retourné d'erreur
		while (false !== ($file = readdir($dossier))) :
					
			// On analyse le nom du fichier
			$file = explode('.', $file);
			
			// On vérifie que le fichier est bien un script .ajax.php
			if ($file[1] == 'ajax' && $file[2] == 'php') :
			
				// Si oui, on rajoute le script à la liste des scripts
				$scripts[] = $file[0];
			
			endif;
		
		endwhile;
	
	endif;
?>
	<ul id="liste-fichiers">
		<?php foreach ($scripts as $file) : ?>
		<li data-file="<?php echo $file; ?>"><?php echo $file; ?></li>
		<?php endforeach; ?>
	</ul>
	<div id="analyse"></div>
<?php
	// On importe le footer
	$core->tpl_footer();
	
	// On importe la purge
	require_once 'purge.php';
?>