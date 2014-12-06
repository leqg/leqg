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
			<li class="electeur"><span>Électeurs</span><span><strong><?php echo number_format($electeurs, 0, ',', ' '); ?></strong> <em>électeur<?php if ($electeurs > 1) { ?>s<?php } ?> importé<?php if ($electeurs > 1) { ?>s<?php } ?></em></span></li>
			<li class="email"><span>Emails recueillis</span><span><strong><?php echo number_format($emails, 0, ',', ' '); ?></strong> <em>contact<?php if ($emails > 1) { ?>s<?php } ?></em></span></li>
			<li class="mobile"><span>Mobiles recueillis</span><span><strong><?php echo number_format($mobiles, 0, ',', ' '); ?></strong> <em>contact<?php if ($mobiles > 1) { ?>s<?php } ?></em></span></li>
			<li class="fixe"><span>Fixes recueillis</span><span><strong><?php echo number_format($fixes, 0, ',', ' '); ?></strong> <em>contact<?php if ($fixes > 1) { ?>s<?php } ?></em></span></li>
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
	// Mise en place de la map
	var map = L.map('mapbox-carto');
	
	// Sélection du tile layer OSM   
	L.tileLayer('http://otile3.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png').addTo(map);

	// On récupère sur le Nominatim OSM les coordonnées de la rue en question
	var data = {
		format: 'json',
		email: 'tech@leqg.info',
		country: 'France',
		city: "<?php echo $ville['commune_nom']; ?>"
	}
	
	// On récupère le JSON contenant les coordonnées de la rue
	$.getJSON('https://nominatim.openstreetmap.org', data, function(data) {
		// On récupère uniquement les données du premier résultat
		data = data[0];
		
		// On prépare la boundingbox
		var loc1 = new L.LatLng(data.boundingbox[0], data.boundingbox[2]);
		var loc2 = new L.LatLng(data.boundingbox[1], data.boundingbox[3]);
		var bounds = new L.LatLngBounds(loc1, loc2);
		
		// On fabrique une vue qui contient l'ensemble du secteur demandé
		map.fitBounds(bounds, { maxZoom: 17 });
		
		// On ajoute un marker au milieu de la rue
		L.marker([data.lat, data.lon], {
			clicable: false,
			title: "<?php echo mb_convert_case($ville['commune_nom'], MB_CASE_TITLE); ?>"
		}).addTo(map);
	});
</script>

<?php Core::tpl_footer(); ?>