<?php
	// Protection de la page
	User::protection(5);
	
	// On récupère les données génériques sur la ville
	$ville = Carto::ville_secure($_GET['code']);
	$departement = Carto::departement($ville['departement_id']);
	$region = Carto::region($departement['region_id']);
	
	// On récupère les statistiques
	$electeurs = Carto::nombreElecteurs('commune', $ville['commune_id']);
	$emails = Carto::nombreElecteurs('commune', $ville['commune_id'], 'email');
	$mobiles = Carto::nombreElecteurs('commune', $ville['commune_id'], 'mobile');
	$fixes = Carto::nombreElecteurs('commune', $ville['commune_id'], 'fixe');
	
	// Chargement du template
	Core::tpl_header();
?>

<h2><?php echo mb_convert_case($ville['commune_nom'], MB_CASE_TITLE); ?></h2>

<div class="colonne demi gauche">
	<section class="contenu">
		<h4>Informations générales</h4>
		<ul class="informations">
			<li class="code"><span>Code INSEE</span><span><?php echo $ville['commune_id']; ?></span></li>
			<li class="region"><span>Département</span><span><?php echo $departement['departement_nom']; ?> (<?php echo $region['region_nom']; ?>)</span></li>
			<li class="electeur"><span>Électeurs</span><span><strong><?php echo number_format($electeurs, 0, ',', ' '); ?></strong> électeur<?php if ($electeurs > 1) { ?>s<?php } ?> importé<?php if ($electeurs > 1) { ?>s<?php } ?></span></li>
			<li class="email"><span>Emails recueillis</span><span><strong><?php echo number_format($emails, 0, ',', ' '); ?></strong> contact<?php if ($emails > 1) { ?>s<?php } ?></span></li>
			<li class="mobile"><span>Mobiles recueillis</span><span><strong><?php echo number_format($mobiles, 0, ',', ' '); ?></strong> contact<?php if ($mobiles > 1) { ?>s<?php } ?></span></li>
			<li class="fixe"><span>Fixes recueillis</span><span><strong><?php echo number_format($fixes, 0, ',', ' '); ?></strong> contact<?php if ($fixes > 1) { ?>s<?php } ?></span></li>
		</ul>
	</section>
	
	<section id="mapbox-carto" class="contenu demi grande"></section>
</div>

<div class="colonne demi droite">
	
	<section class="contenu demi">
		<h4>Recherchez une rue dans cette commune</h4>
		
		<ul class="formulaire">
			<li>
				<label for="rechercheRue" class="small">Rue à chercher</label>
				<span class="form-icon decalage rue"><input type="text" name="rechercheRue" id="rechercheRue" class="rechercheRue" placeholder="rue du Marché, par exemple" data-ville="<?php echo $ville['commune_id']; ?>">
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible resultatsRues">
		<h4>Rues correspondant à la recherche</h4>
		
		<ul class="listeDesRues form-liste"></ul>
	</section>
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