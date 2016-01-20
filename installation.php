<?php
    // On importe d'abord les fichiers associés
    require_once 'includes.php';
    
    // On importe le header
    $core->loadHeader();
?>

	<div id="installation" style="margin: 3em;">
		<h2 id="titre">Installation du système central LeQG</h2>
		<h3 id="titre">Mise en place de la base de données et des données cartographiques</h3>
		<div id="install" style="margin: 1em 5%;">
			<span id="demarrage">Cliquez ici pour lancer l'installation</span>
		</div>
	</div>
	
	<script src="assets/js/installation.js"></script>

<?php
    // On importe le footer
    $core->loadFooter();
    
    // On importe la purge
    require_once 'purge.php';
?>
