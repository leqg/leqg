<?php 
	// Chargement de l'objet contact
	$contact = new contact($_GET['contact']);

	// Chargement de l'entête
	$core->tpl_header();
?>

<h2 id="nomContact" data-fiche="<?php echo $contact->contact['contact_id']; ?>"><?php echo $contact->noms(); ?></h2>

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
			<li class="naissance"><?php if ($contact->contact['contact_naissance_date'] != '0000-00-00') { echo $contact->naissance(); } else { echo '<span class="inconnu">Date de naissance inconnue</span>'; } ?></li>
			<li class="age"><?php if ($contact->contact['contact_naissance_date'] != '0000-00-00') { echo $contact->age(); } else { echo '<span class="inconnu">Âge inconnu</span>'; } ?></li>
			<?php if ($contact->contact['adresse_id']) { ?><li class="adresse"><?php echo $contact->adresse('declaree'); ?></li><?php } ?>
			<?php if (!empty($contact->contact['contact_organisme'])) { ?><li class="organisme"><?php echo utf8_encode($contact->contact['contact_organisme']); ?> <?php if (!empty($contact->contact['contact_fonction'])) { echo $contact->contact['contact_fonction']; } ?></li><?php } ?>
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
	
	<section id="fichesLiees" class="contenu demi">
		<h4>Fiches liées</h4>
		
		<ul class="etatcivil">
			<?php $fiches = $contact->fichesLiees(); foreach ($fiches as $identifiant => $fiche) : ?>
			<li class="lien"><a href="<?php Core::tpl_go_to('contact', array('contact' => md5($identifiant))); ?>"><?php echo strtoupper($fiche['contact_nom']); ?> <?php echo strtoupper($fiche['contact_nom_usage']); ?> <?php echo ucwords(strtolower($fiche['contact_prenoms'])); ?></a></li>
			<?php endforeach; ?>
			<li class="ajout ajouterLien">Ajouter une nouvelle fiche liée</li>
		</ul>
	</section>
</div>


<div id="colonneDroite" class="colonne demi droite">
	<section id="carte" class="contenu demi"></section>
	
	<section id="TagsContact" class="contenu demi">
		<h4>Tags liés au contact</h4>
		
		<ul class="listeDesTags">
			<?php $tags = explode(',', $contact->contact['contact_tags']); if (!empty($contact->contact['contact_tags'])) : foreach ($tags as $tag) : ?>
			<li class="tag"><?php echo $tag; ?></li>
			<?php endforeach; endif; ?>
			<li class="ajout ajouterTag">Ajouter un nouveau tag</li>
		</ul>
	</section>
	
	<section id="listeEvenements" class="contenu demi">
		<h4>Événements connus</h4>
		
		<button class="nouvelEvenement new">Créer un nouvel événement</button>
		
		<ul class="listeDesEvenements">
			<?php $events = $contact->listeEvenements(); if (count($events) >= 1) : foreach ($events as $event) : $event = new evenement($event['historique_id'], false); ?>
			<?php if ($event->lien()) { ?><a href="#" class="accesEvenement nostyle" data-evenement="<?php echo md5($event->get_infos('id')); ?>"><?php } ?>
				<li class="evenement <?php echo $event->get_infos('type'); ?> <?php if ($event->lien()) { ?>clic<?php } ?>">
					<small><span><?php echo Core::tpl_typeEvenement($event->get_infos('type')); ?></span></small>
					<strong><?php echo (!empty($event->get_infos('objet'))) ? $event->get_infos('objet') : Core::tpl_typeEvenement($event->get_infos('type')); ?></strong>
					<ul class="infosAnnexes">
						<li class="date"><?php echo date('d/m/Y', strtotime($event->get_infos('date'))); ?></li>
						<?php if (!empty($event->get_infos('lieu'))) { ?><li class="lieu"><?php echo $event->get_infos('lieu'); ?></li><?php } ?>
					</ul>
				</li>
			<?php if ($event->lien()) { ?></a><?php } ?>
			<?php endforeach; else : ?>
			<li class="evenement"><strong>Aucun événement connu</strong></li>
			<?php endif; ?>
		</ul>
	</section>
	
	<section id="evenement" class="contenu demi invisible" data-evenement="">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="eventInfos formulaire small">
			<li>
				<label class="small" for="eventTitre">Objet</label>
				<span class="form-icon objet">
					<input type="text" name="titre" id="eventTitre" value="">
				</span>
			</li>
			<li>
				<label class="small" for="eventType">Type</label>
				<select name="type" id="eventType">
					<option value="contact">Entrevue</option>
					<option value="telephone">Contact téléphonique</option>
					<option value="email">Courrier électronique</option>
					<option value="courrier">Correspondance postale</option>
					<option value="autre">Autre</option>
				</select>
			</li>
			<li>
				<label class="small" for="eventLieu">Lieu</label>
				<span class="form-icon lieu">
					<input type="text" name="lieu" id="eventLieu" value="">
				</span>
			</li>
			<li>
				<label class="small" for="eventDate">Date</label>
				<span class="form-icon date">
					<input type="text" name="date" id="eventDate" value="" placeholder="jj/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}">
				</span>
			</li>
			<li>
				<label class="small" for="eventNotes">Notes</label>
				<span class="form-icon notes">
					<textarea name="notes" id="eventNotes"></textarea>
				</span>
			</li>
			<li>
				<button class="supprimerEvenement long deleting">Supprimer l'événement</button>
			</li>
		</ul>
	</section>

	<section id="ChercherFicheALier" class="contenu demi invisible">
		<ul class="formulaire">
			<li>
				<label>Recherchez une fiche à lier</label>
				<span class="form-icon search"><input type="text" name="rechercheFiche" id="rechercheFiche" placeholder="Pierre Dupont"></span>
			</li>
		</ul>
		<ul class="form-liste invisible" id="listeFichesALier"></ul>
	</section>
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