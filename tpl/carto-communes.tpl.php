<?php
	// Protection de la page
	User::protection(5);
	
	// On récupère les données génériques sur la ville
	$ville = Carto::ville_secure($_GET['code']);
	
	// Chargement du template
	Core::tpl_header();
?>

<h2><?php echo mb_convert_case($ville['commune_nom'], MB_CASE_TITLE); ?></h2>

<div class="colonne demi gauche">
	<section class="contenu">
		<h4>Informations générales</h4>
	</section>
</div>

<div class="colonne demi droite">
	<section id="mapbox-carto" class="contenu demi grande"></section>
</div>

<script>
	// Token public d'accès à Mapbox
	L.mapbox.accessToken = 'pk.eyJ1IjoiaGl3ZWxvIiwiYSI6Imc3M3EzbmsifQ.t1k5I2FxgVdFfl6QNBA_Ew';
	
	// On met en place la map
	var geocoder = L.mapbox.geocoder('mapbox.places-v1'),
	    map = L.mapbox.map('mapbox-carto', 'hiwelo.k8fnkd96');
	
	// On recherche la ville en question pour l'afficher avec la fonction showMap
	geocoder.query('<?php echo $ville['commune_nom']; ?>', showMap);
	
	function showMap(err, data) {
		// The geocoder can return an area, like a city, or a
		// point, like an address. Here we handle both cases,
		// by fitting the map bounds to an area or zooming to a point.
		if (data.lbounds) {
			map.fitBounds(data.lbounds);
		} else if (data.latlng) {
			map.setView([data.latlng[0], data.latlng[1]], 13);
		}
	}
</script>

<?php Core::tpl_footer(); ?>