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
			if ($file[1] == 'csv') :
			
				// Si oui, on rajoute le script à la liste des scripts
				$scripts[] = $file[0];
			
			endif;
		
		endwhile;
		
		natsort($scripts);
	
	endif;
?>
	<ul id="liste-fichiers">
		<?php foreach ($scripts as $file) : ?>
		<li data-file="<?php echo $file; ?>"><?php echo $file; ?></li>
		<?php endforeach; ?>
	</ul>
	<div id="analyse" style="margin: 1.5em;"></div>
	<script>
		$("#liste-fichiers li").click(function(){
			var file =  $(this).data('file');
			
			$("#analyse").html('Fichier CSV en cours d\'import...<br>');
			
			$.ajax({
				type: 'POST',
				url: 'ajax.php?script=import-csv',
				data: { 'fichier': file },
				dataType: 'html'
			}).done(function(data){
				$("#analyse").append('Fichier CSV importé !<br>');
			});
		});
	</script>
<?php
	// On importe le footer
	$core->tpl_footer();
	
	// On importe la purge
	require_once 'purge.php';
?>