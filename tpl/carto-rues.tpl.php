<?php
	// Protection de la page
	User::protection(5);
	
	// On récupère les données génériques sur la rue
	$rue = Carto::rue_secure($_GET['code']);
	$ville = Carto::ville($rue['commune_id']);
	$departement = Carto::departement($ville['departement_id']);
	$region = Carto::region($departement['region_id']);
	
	// On récupère les statistiques
	$electeurs = Carto::nombreElecteurs('rue', $rue['rue_id']);
	$emails = Carto::nombreElecteurs('rue', $rue['rue_id'], 'email');
	$mobiles = Carto::nombreElecteurs('rue', $rue['rue_id'], 'mobile');
	$fixes = Carto::nombreElecteurs('rue', $rue['rue_id'], 'fixe');
	
	// Chargement du template
	Core::tpl_header();
?>

<h2><?php echo mb_convert_case(trim($rue['rue_nom']), MB_CASE_TITLE); ?></h2>

<div class="colonne demi gauche">
	<section class="contenu">
		<h4>Informations générales</h4>
		<ul class="informations">
			<li class="ville"><span>Ville</span><span><a href="<?php Core::tpl_go_to('carto', array('niveau' => 'communes', 'code' => hash('sha256', $ville['commune_id']))); ?>" class="nostyle"><strong><?php echo $ville['commune_nom']; ?></strong></a></span></li>
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
		<h4>Immeubles connus dans cette rue</h4>

		<ul class="listeDesImmeubles form-liste">
			<?php $immeubles = Carto::listeImmeubles($rue['rue_id']); foreach ($immeubles as $immeuble) : $contactsDansImmeuble = Carto::coordonneesDansImmeuble($immeuble['immeuble_id']); ?>
			<li <?php if ($contactsDansImmeuble) echo 'class="presenceContacts"'; ?>>
				<span><strong class="immeuble"><?php echo $immeuble['numero']; ?></strong> <?php echo mb_convert_case($rue['rue_nom'], MB_CASE_TITLE); ?></span>
				<a href="<?php Core::tpl_go_to('carto', array('niveau' => 'immeubles', 'code' => hash('sha256', $immeuble['immeuble_id']))); ?>" class="nostyle">
					<button>Explorer</button>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
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
		city: "<?php echo $ville['commune_nom']; ?>",
		street: "<?php echo $rue['rue_nom']; ?>"
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
			title: "<?php echo mb_convert_case($rue['rue_nom'], MB_CASE_TITLE); ?>"
		}).addTo(map);
	});
</script>

<?php Core::tpl_footer(); ?>