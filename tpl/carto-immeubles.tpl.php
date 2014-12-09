<?php
	// Protection de la page
	User::protection(5);
	
	// On récupère les données génériques sur la rue
	$immeuble = Carto::immeuble_secure($_GET['code']);
	$rue = Carto::rue($immeuble['rue_id']);
	$ville = Carto::ville($rue['commune_id']);
	$departement = Carto::departement($ville['departement_id']);
	$region = Carto::region($departement['region_id']);
	
	// On récupère les statistiques
	$electeurs = Carto::nombreElecteurs('immeuble', $immeuble['immeuble_id']);
	$emails = Carto::nombreElecteurs('immeuble', $immeuble['immeuble_id'], 'email');
	$mobiles = Carto::nombreElecteurs('immeuble', $immeuble['immeuble_id'], 'mobile');
	$fixes = Carto::nombreElecteurs('immeuble', $immeuble['immeuble_id'], 'fixe');
	
	// Chargement du template
	Core::tpl_header();
?>

<h2><?php echo $immeuble['immeuble_numero']; ?> <?php echo mb_convert_case(trim($rue['rue_nom']), MB_CASE_TITLE); ?></h2>

<div class="colonne demi gauche">
	<section class="contenu">
		<h4>Informations générales</h4>
		<ul class="informations">
			<li class="rue"><span>Rue</span><span><a href="<?php Core::tpl_go_to('carto', array('niveau' => 'rues', 'code' => hash('sha256', $rue['rue_id']))); ?>" class="nostyle"><strong><?php echo $rue['rue_nom']; ?></strong></a></span></li>
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
		<h4>Électeurs déclarés dans l'immeuble</h4>

		<ul class="listeContacts">
			<?php $contacts = Carto::listeElecteurs($immeuble['immeuble_id']); foreach ($contacts as $contact) : $classSexe = array('M' => 'homme', 'F' => 'femme', 'i' => 'isexe'); ?>
			<a href="<?php Core::tpl_go_to('contact', array('contact' => $contact['code'])); ?>" class="nostyle">
				<li class="contact <?php echo $classSexe[$contact['contact_sexe']]; ?>">
					<?php if (!empty($contact['contact_nom']) || !empty($contact['contact_nom_usage']) || !empty($contact['contact_prenoms'])) : ?>
					<strong><?php echo mb_convert_case($contact['contact_nom'], MB_CASE_UPPER); ?> <?php echo mb_convert_case($contact['contact_nom_usage'], MB_CASE_UPPER); ?> <?php echo mb_convert_case($contact['contact_prenoms'], MB_CASE_TITLE); ?></strong>
					<?php elseif (!empty($contact['contact_organisme'])) : ?>
					<strong><?php echo $contact['contact_organisme']; ?></strong>
					<?php else : ?>
					<strong>Fiche sans nom</strong>
					<?php endif; ?>
				</li>
			</a>
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
		street: "<?php echo $immeuble['immeuble_numero'] . ' ' . $rue['rue_nom']; ?>"
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
			title: "<?php echo mb_convert_case($immeuble['immeuble_numero'] . ' ' . $rue['rue_nom'], MB_CASE_TITLE); ?>"
		}).addTo(map);
	});
</script>

<?php Core::tpl_footer(); ?>