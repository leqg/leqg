<?php
	// Protection de la page
	User::protection(5);
	
	// On récupère les données génériques sur le bureau
	$bureau = Carto::bureau_secure($_GET['code']);
	$ville = Carto::ville($bureau['commune_id']);
	$departement = Carto::departement($ville['departement_id']);
	$region = Carto::region($departement['region_id']);
	
	// On récupère les statistiques
	$electeurs = Carto::nombreElecteurs('bureau', $bureau['bureau_id']);
	$emails = Carto::nombreElecteurs('bureau', $bureau['bureau_id'], 'email');
	$mobiles = Carto::nombreElecteurs('bureau', $bureau['bureau_id'], 'mobile');
	$fixes = Carto::nombreElecteurs('bureau', $bureau['bureau_id'], 'fixe');
	
	// Chargement du template
	Core::tpl_header();
?>

<h2 data-bureau="<?php echo $bureau['bureau_id']; ?>">Bureau <?php echo $bureau['bureau_numero']; ?> <?php echo mb_convert_case(trim($bureau['bureau_nom']), MB_CASE_TITLE); ?></h2>

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
	
	<section id="mapbox-carto"></section>
</div>

<div class="colonne demi droite">
	<section class="contenu demi">
		<h4>Contacts connus du bureau</h4>
		
		<ul class="listeContacts">
			<?php $adresses = array(); $contacts = Carto::listeElecteursParBureau($bureau['bureau_id'], true); foreach ($contacts as $contact) : $classSexe = array('M' => 'homme', 'F' => 'femme', 'i' => 'isexe'); $c = new Contact($contact['code']); $adresses[] = explode('|', $c->adresse('electorale', '|')); ?>
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

	<?php $i = 1; foreach ($adresses as $adresse) : $ville = explode(' ', $adresse[1], 2); ?>
	// On récupère sur le Nominatim OSM les coordonnées de la rue en question
	var data<?php echo $i; ?> = {
		format: 'json',
		email: 'tech@leqg.info',
		country: 'France',
		city: "<?php echo $ville[1]; ?>",
		street: "<?php echo $adresse[0]; ?>"
	}
	
	// On récupère le JSON contenant les coordonnées de la rue
	$.getJSON('https://nominatim.openstreetmap.org', data<?php echo $i; ?>, function(data<?php echo $i; ?>) {
		// On récupère uniquement les données du premier résultat
		data<?php echo $i; ?> = data<?php echo $i; ?>[0];
		
		// On prépare la boundingbox
		var loc1 = new L.LatLng(data<?php echo $i; ?>.boundingbox[0], data<?php echo $i; ?>.boundingbox[2]);
		var loc2 = new L.LatLng(data<?php echo $i; ?>.boundingbox[1], data<?php echo $i; ?>.boundingbox[3]);
		var bounds = new L.LatLngBounds(loc1, loc2);
		
		// On fabrique une vue qui contient l'ensemble du secteur demandé
		map.fitBounds(bounds, { maxZoom: 15 });
		
		// On ajoute un marker au milieu de la rue
		L.marker([data<?php echo $i; ?>.lat, data<?php echo $i; ?>.lon], {
			clicable: false,
			title: "<?php echo $adresse[0] . ' ' . $adresse[1]; ?>"
		}).addTo(map);
	});
	<?php $i++; endforeach; ?>
</script>


<?php Core::tpl_footer(); ?>