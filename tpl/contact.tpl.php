<?php 
	// Chargement de l'objet contact
	$contact = new contact($_GET['contact']);

	// Chargement de l'entête
	$core->tpl_header();
?>

<h2><?php echo $contact->noms(); ?></h2>

<div class="colonne demi gauche">
	<section id="fiche-details" class="contenu demi">
		<ul class="icones-etatcivil">
			<li class="sexe <?php if ($contact->contact['contact_sexe'] == 'M') { echo 'homme'; } else if ($contact->contact['contact_sexe'] == 'F') { echo 'femme'; } else { echo 'inconnu'; } ?>"><?php if ($contact->contact['contact_sexe'] == 'M') { echo 'Homme'; } else if ($contact->contact['contact_sexe'] == 'F') { echo 'Femme'; } else { echo 'Sexe'; } ?></li>
			<li class="electeur <?php if ($contact->contact['contact_electeur']) { echo 'oui'; } else { echo 'non'; } ?>">Électeur</li>
			<li class="sms <?php if ($contact->possede('mobile')) { ?>envoyerSMS<?php } ?>">SMS</li>
			<li class="email <?php if ($contact->possede('email')) { ?>envoyerEmail<?php } ?>">Email</li>
		</ul>
	
		<h4>Données connues</h4>
		<ul class="etatcivil">
			<li class="naissance"><?php echo $contact->naissance(); ?></li>
			<li class="age"><?php echo $contact->age(); ?></li>
			<li class="adresse"><?php echo $contact->adresse('declaree'); ?></li>
		</ul>
		
		<?php if ($contact->contact['contact_electeur'] == 1) : ?>
		<h4>Données électorales</h4>
		<ul class="etatcivil">
			<li class="bureau"><?php echo $contact->bureau(); ?></li>
			<li class="adresse"><?php echo $contact->adresse('electorale'); ?></li>
		</ul>
		<?php endif; ?>
		
		<h4>Données de contact</h4>
		<ul class="etatcivil">
			<?php $coordonnees = $contact->coordonnees(); foreach ($coordonnees as $coordonnee) : ?>
			<li class="<?php echo $coordonnee['coordonnee_type']; ?>">
				<?php 
				if ($coordonnee['coordonnee_type'] == 'email')
				{
					echo $coordonnee['coordonnee_email'];
				}
				else
				{ 
					Core::tpl_phone($coordonnee['coordonnee_numero']); 
				} 
				?>
			</li>
			<?php endforeach; ?>
			<li class="ajout ajouterCoordonnees">Ajouter une nouvelle information de contact</li>
		</ul>
	</section>
	
	<section id="carte" class="contenu demi"></section>
</div>


<script>
	function initialize() {
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(48.58476, 7.750576);
		var mapOptions = {
			//center: latlng,
			disableDefaultUI: true,
			draggable: true,
			rotateControl: false,
			scrollwheel: false,
			zoomControl: true,
			zoom: 17
		};
		var map = new google.maps.Map(document.getElementById("carte"), mapOptions);
		
		// On marque les différents bâtiments
		// L'adresse à rechercher
		var GeocoderOptions = { 'address': "<?php if ($contact->contact['adresse_id'] == 0) { echo $contact->adresse('electorale', ' '); } else { echo $contact->adresse('declaree', ' '); } ?>", 'region': 'FR' };
		// La function qui va traiter le résultat
		function GeocodingResult(results, status) {
			// Si la recherche a fonctionnée
			if (status == google.maps.GeocoderStatus.OK) {
				// On créé un nouveau marker sur la map
				markerAdresse = new google.maps.Marker({
				position: results[0].geometry.location,
				map: map,
				title: "<?php echo $contact->noms(); ?>"
				});
				// On centre sur ce marker
				map.setCenter(results[0].geometry.location);
			}
		}
		// On lance la recherche de l'adresse
		geocoder.geocode(GeocoderOptions, GeocodingResult);
	}
	google.maps.event.addDomListener(window, 'load', initialize);
</script>

<?php $core->tpl_footer(); ?>