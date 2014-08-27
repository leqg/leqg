<?php
	$parcours = $mission->chargement($_GET['mission']);
	$immeubles = explode(',', $parcours['immeubles']);
	
	$afaire = explode(',', $parcours['a_faire']);
	$fait = explode(',', $parcours['fait']);
?>
<section id="fiche">
	<header class="porte">
		<h2>
			<span>Boîtage</span>
			<span>Mission <?php echo $parcours['id']; ?></span>
		</h2>
	</header>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Ville</span>
			<p><?php echo $carto->afficherVille($parcours['ville_id']); ?></p>
		</li>
		<li>
			<span class="label-information">Rue</span>
			<p><?php echo $carto->afficherRue($parcours['rue_id']); ?></p>
		</li>
		<li>
			<span class="label-information">Immeubles</span>
			<ul class="listeEncadree">
				<?php foreach ($immeubles as $immeuble) : ?>
					<?php if (in_array($immeuble, $afaire)) : ?><a href="ajax.php?script=boitage-fait&mission=<?php echo $parcours['id']; ?>&immeuble=<?php echo $immeuble; ?>" title="Cliquez pour marquer l'immeuble comme fait"><?php endif; ?>
						<li class="boitage immeuble <?php echo (in_array($immeuble, $afaire)) ? 'afaire' : 'fait'; ?>">
							<strong><?php $carto->afficherImmeuble($immeuble); ?> <?php $carto->afficherRue($parcours['rue_id']); ?></strong>
						</li>
					<?php if (in_array($immeuble, $afaire)) : ?></a><?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</section>

<?php if (isset($_GET['immeuble'])) : ?>
	<aside>
		<div>
			<nav class="navigationFiches">
				<a class="retour" href="<?php $core->tpl_go_to('porte', array('action' => 'mission', 'mission' => $_GET['mission'])); ?>">Quitter l'adresse</a>
			</nav>
			
			<?php $immeuble = $carto->immeuble($_GET['immeuble']); $electeurs = $building[$immeuble['id']]; ?>
			<h6><?php echo $immeuble['numero']; ?> <?php $carto->afficherRue($immeuble['rue_id']); ?></h6>
			
			<form action="ajax.php?script=porte-reporting&mission=<?php echo $_GET['mission']; ?>" method="post">
				<ul class="listeEncadree">
					<?php foreach ($electeurs as $electeur) : ?>
					<li class="electeur">
						<strong><?php $fiche->nomByID($electeur); ?></strong>
						<ul class="boutonsRadio"><!--
						 --><label for="vu-electeur-<?php echo $electeur; ?>"><li><input type="radio" name="<?php echo $electeur; ?>" value="2" id="vu-electeur-<?php echo $electeur; ?>"> Vu</li><!--
						 --><label for="absent-electeur-<?php echo $electeur; ?>"><li><input type="radio" name="<?php echo $electeur; ?>" value="1" id="absent-electeur-<?php echo $electeur; ?>"> Absent</li><!--
						 --><label for="afaire-electeur-<?php echo $electeur; ?>"><li><input type="radio" name="<?php echo $electeur; ?>" value="0" id="afaire-electeur-<?php echo $electeur; ?>" checked> À faire</li><!--
					 --></ul>
					</li>
					<?php endforeach; ?>
				</ul>
				<ul class="deuxColonnes" style="padding-left: 0px;">
					<li class="submit">
						<input type="submit" value="Valider le rapport">
					</li>
				</ul>
			</form>
		</div>
	</aside>
<?php else: ?>
	<aside>
		<div>
			<nav class="navigationFiches">
				<a class="retour" href="<?php $core->tpl_go_to('porte', array('action' => 'missions')); ?>">Retour aux missions</a>
			</nav>
			
			<div id="carte" style="width: 100%; height: 400px; background-color: gray; margin-top: 3em;"></div>
		</div>
	</aside>
	<script>
		// Script relatif à l'affichage de la carte par utilisateur
		function initialize() {
			// On récupère les data liées à la carte
			var nom_electeur = $("#carte").data('nom');
			var adresse_electeur = $("#carte").data('adresse');
		
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
			
			// La function qui va traiter le résultat
			function GeocodingResult(results, status) {
				// Si la recherche a fonctionnée
				if (status == google.maps.GeocoderStatus.OK) {
					// On créé un nouveau marker sur la map
					markerAdresse = new google.maps.Marker({
						position: results[0].geometry.location,
						map: map,
						title: 'Immeuble à visiter'
					});
					
					// On centre sur ce marker
					map.setCenter(results[0].geometry.location);
				}
			}
			
			
			<?php foreach ($afaire as $immeuble) : ?>
			
			var GeocoderOptions<?php echo $immeuble; ?> = { 'address': "<?php $carto->afficherImmeuble($immeuble); ?> <?php $carto->afficherRue($parcours['rue_id']); ?> <?php $carto->afficherVille($parcours['ville_id']); ?>", 'region': 'FR' };
			
			// On lance la recherche de l'adresse
			geocoder.geocode(GeocoderOptions<?php echo $immeuble; ?>, GeocodingResult);
			
			<?php endforeach; ?>
			
		}
		
		google.maps.event.addDomListener(window, 'load', initialize);
	</script>
<?php endif; ?>