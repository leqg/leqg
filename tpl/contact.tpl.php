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
			<?php if ($contact->contact['adresse_id']) { ?><li class="adresse"><?php echo $contact->adresse('declaree'); ?></li><?php } ?>
		</ul>
		
		<?php if ($contact->contact['contact_electeur'] == 1) : ?>
		<h4>Données électorales</h4>
		<ul class="etatcivil">
			<li class="bureau"><?php echo $contact->bureau(); ?></li>
			<li class="adresse"><?php echo $contact->adresse('electorale'); ?></li>
		</ul>
		<?php endif; ?>
		
		<h4>Données de contact</h4>
		<ul class="etatcivil coordonnees">
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
</div>

<div class="colonne demi droite">
	<section id="carte" class="contenu demi"></section>
</div>

<!-- Formulaires en overlay -->
<div id="ajoutCoordonnees" class="overlayForm">
	<form id="ajoutDeCoordonnees" method="post" action="ajax.php?script=coordonnees–ajout">
		<input type="hidden" name="fiche" id="idFiche" value="<?php echo $_GET['contact']; ?>">
		<a class="fermetureOverlay" href="#">&#xe813;</a>
		<h3>Ajout d'un moyen de contact</h3>
		<ul>
			<li>
				<label>Type de coordonnées</label>
				<div class="radio"><input class="selectionType" data-type="email" type="radio" name="type" id="ajoutCoordonneesEmail" value="email" required><label for="ajoutCoordonneesEmail"><span><span></span></span>Adresse email</label></div>
				<div class="radio"><input class="selectionType" data-type="telephone" type="radio" name="type" id="ajoutCoordonneesMobile" value="mobile" required><label for="ajoutCoordonneesMobile"><span><span></span></span>Téléphone mobile</label></div>
				<div class="radio"><input class="selectionType" data-type="telephone" type="radio" name="type" id="ajoutCoordonneesFixe" value="fixe" required><label for="ajoutCoordonneesFixe"><span><span></span></span>Téléphone fixe</label></div>
			</li>
			<li class="detail-critere detail-critere-email affichageOptionnel">
				<label for="form-modifier-email">Adresse email</label>
				<input type="email" name="email" id="form-ajout-email" autocomplete="off">
			</li>
			<li class="detail-critere detail-critere-telephone affichageOptionnel">
				<label for="form-modifier-email">Numéro de téléphone</label>
				<input type="text" name="numero" id="form-ajout-telephone" autocomplete="off">
			</li>
			<li>
				<input type="submit" value="Ajouter l'information">
			</li>
		</ul>
	</form>
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
			zoom: 16
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
				title: "<?php echo $contact->noms(' ', ' '); ?>"
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