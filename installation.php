<?php
	// On importe d'abord les fichiers associés
	require_once 'includes.php';
	
	// On importe le header
	$core->tpl_header();
?>

	<div id="installation" style="margin: 3em;">
		<h2 id="titre">Installation des données cartographiques</h2>
		<h3 id="titre">Mise en place des régions</h3>
		<div id="install" style="margin: 1em 5%;">
			<span id="demarrage">Cliquez ici pour lancer l'installation</span>
		</div>
	</div>
	
	<script src="assets/js/installation.js"></script>

<?php
	// On importe le footer
	$core->tpl_footer();
	
	// On importe la purge
	require_once 'purge.php';
?>