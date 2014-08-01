<?php $core->tpl_header(); ?>

<section id="fiche-electeur">
	<header>
		<h2><?php $fiche->affichage_nom('span'); ?></h2>
	
		<div id="electeur-icons">
			<?php if ($fiche->get_infos('electeur')) : ?><span id="est-electeur" title="Électeur">&#xe840;</span><?php endif; ?>
		</div>
	</header>

	<ul class="infos">
		<li>
			<label>Sexe</label>
			<?php $fiche->sexe(); ?>
		</li>
		<li>
			<label>Date de naissance</label>
			<?php $fiche->date_naissance(' / '); ?> <?php $fiche->lieu_de_naissance('à'); ?><br>
			<?php $fiche->age(); ?>
		</li>
		<li>
			<label>Adresse déclarée</label>
			<?php $fiche->affichage_adresse(); ?>
		</li>
		<li>
			<label>Bureau de vote</label>
			<?php $fiche->bureau(true); ?><br>
			<?php $fiche->canton(); ?>
		</li>
		<li>
			<label>Adresse email</label>
			<input class="fiche" type="email" name="email" id="form-email" placeholder="abc@domaine.fr" value="<?php $fiche->contact('email'); ?>">
			<span id="valider-form-email">Valider</span>
			<span id="reussite-form-email">&#xe812;</span>
		</li>
		<li>
			<label>Téléphone portable</label>
			<input class='fiche' type='text' name='mobile' id='form-mobile' placeholder='00 00 00 00 00' value='<?php $core->tpl_phone($fiche->contact('mobile', false, true)); ?>'>
			<span id="valider-form-mobile">Valider</span>
			<span id="reussite-form-mobile">&#xe812;</span>
		</li>
		<li>
			<label>Téléphone fixe</label>
			<input class='fiche' type='text' name='telephone' id='form-telephone' placeholder='00 00 00 00 00' value='<?php $core->tpl_phone($fiche->contact('telephone', false, true)); ?>'>
			<span id="valider-form-telephone">Valider</span>
			<span id="reussite-form-telephone">&#xe812;</span>
		</li>
	</ul>
</section>

<aside class="ficheContact">
	<?php
		// On regarde s'il existe un historique avec ce contact, ou des fichiers, et si ce n'est pas le cas, on charge un bouton pour lancer la première interaction
		$nombre['dossier'] = $dossier->nombre( $fiche->get_infos('id') );
		$nombre['historique'] = $historique->nombre( $fiche->get_infos('id') );
		
		// S'il n'existe aucun historique, on charge le volet correspondant
		if ( $nombre['dossier'] == 0 && $nombre['historique'] == 0 ) :
			$core->tpl_load( 'aside' , 'premiercontact' );
		
		// S'il existe des éléments d'histoire ou de dossiers avec le contact choisi, on lance les volets habituels
		else :

		endif;
	?>
</aside>

<script>
	$(document).ready(function() {
	
		// javascript relatif à la partie Fichier
		
			$("#form-mobile").inputmask("99 99 99 99 99");
			$("#form-telephone").inputmask("99 99 99 99 99");
			$("#form-date").inputmask("9{1,2}/9{1,2}/9{2,4}");
					
			// Au chargement, on cache les marqueurs "valider" sur chaque formulaire
			$("#valider-form-telephone").hide();
			$("#valider-form-mobile").hide();
			$("#valider-form-email").hide();
				
			// Script AJAX de sauvegarde lancé à départ d'un formulaire
			$("#form-email").change(function() {
				// On récupère les informations
				var value = $(this).val();
			
				// On appelle l'AJAX
				$.ajax({
					type: 'POST',
					url: 'ajax-form.php?action=maj-fiche&id=<?php $fiche->infos('id'); ?>',
					data: { champ: 'email', valeur: value },
					dataType: 'html'
				}).done(function(){
					$("#valider-form-email").fadeOut('slow');
					$("#reussite-form-email").fadeIn('slow');
				});
			});
		
		$("#form-telephone").change(function() {
			// On récupère les informations
			var value = $(this).val();
		
			// On appelle l'AJAX
			$.ajax({
				type: 'POST',
				url: 'ajax-form.php?action=maj-fiche&id=<?php $fiche->infos('id'); ?>',
				data: { champ: 'telephone', valeur: value },
				dataType: 'html'
			}).done(function(){
				$("#valider-form-telephone").fadeOut('slow');
				$("#reussite-form-telephone").fadeIn('slow');
			});
		});
		
		$("#form-mobile").change(function() {
			// On récupère les informations
			var value = $(this).val();
		
			// On appelle l'AJAX
			$.ajax({
				type: 'POST',
				url: 'ajax-form.php?action=maj-fiche&id=<?php $fiche->infos('id'); ?>',
				data: { champ: 'mobile', valeur: value },
			}).done(function(){
				$("#valider-form-mobile").fadeOut('slow');
				$("#reussite-form-mobile").fadeIn('slow');
			});
		});
		
		$("#form-email").focus(function(){
			$("#reussite-form-email").fadeOut('slow');
		});
		$("#form-telephone").focus(function(){
			$("#reussite-form-telephone").fadeOut('slow');
		});
		$("#form-mobile").focus(function(){
			$("#reussite-form-mobile").fadeOut('slow');
		});
		
		
		// Quand les champs de formulaire sont sélectionnés, on affiche le bouton valider
		$("#form-email").focus(function(){
			$("#valider-form-email").fadeIn('slow');
		});
		$("#form-telephone").focus(function(){
			$("#valider-form-telephone").fadeIn('slow');
		});
		$("#form-mobile").focus(function(){
			$("#valider-form-mobile").fadeIn('slow');
		});
		
		
		// Simplement quand on sort du formulaire, on supprime le bouton valider
		$("#form-email").blur(function(){
			$("#valider-form-email").fadeOut('slow');
		});
		$("#form-telephone").blur(function(){
			$("#valider-form-telephone").fadeOut('slow');
		});
		$("#form-mobile").blur(function(){
			$("#valider-form-mobile").fadeOut('slow');
		});		
		
		
		// Scripts AJAX d'ajout d'un tag
		$('#add-tag').click(function() {
			$('#ajout-tag').show();
			$('#ajout-tag').focus();
		});
		
		$('.ajout-tag').keypress(function(e){
			if (e.which == 13) { // touche entrée appuyée ou tab
				if ($(this).val() != '') {
					// On récupère le contenu du tag
					var tag = $(this).val();
					
					// On insère le tag dans la base de données et on l'ajoute à la liste
					$('#tags-fiche').append('<span class="tag">' + tag + '</span>');
					$.ajax({
						type: 'POST',
						url: 'ajax-form.php?action=ajout-tag&id=<?php $fiche->infos('id'); ?>',
						data: { valeur: tag }
					});
					
					// On cache le formulaire et on le vide
					$(this).hide();
					$(this).val('');
					
					return true;
				}
			}
			else {
				if (e.which == 44) { // touche virgule appuyée, on enregistre et ouvre un nouveau formulaire
					if ($(this).val() != '') {
						// On récupère le contenu du tag
						var tag = $(this).val();
						
						// On insère le tag dans la base de données et on l'ajoute à la liste
						$('#tags-fiche').append('<span class="tag">' + tag + '</span>');
						$.ajax({
							type: 'POST',
							url: 'ajax-form.php?action=ajout-tag&id=<?php $fiche->infos('id'); ?>',
							data: { valeur: tag }
						});
						
						// On vide le formulaire
						$(this).val('');
						
						return false;
					}
				}
				/*else if (e.which == 0) {
					$(this).hide();
					$(this).val('');
				}*/
				/*else {
					// on met à jour l'autocomplétion
					var entree = $(this).val();
					
					$.ajax({
						type: 'POST',
						url: 'ajax-form.php?action=autocompletion-tag',
						data: { valeur: entree },
						dataType: 'html'
					}).done(function(data){
						$('#list-tag').html(data);
					});
				}*/
			}
		});
		
		// On retire le petit formulaire d'ajout de tag quand il n'est plus focus
		$("#ajout-tag").blur(function(){
			$(this).hide();
			$(this).val('');
		});
		
		$(".tag").click(function(){
			var tag = $(this).html();
			$(this).hide();
			$.ajax({
				type: 'POST',
				url: 'ajax-form.php?action=suppression-tag&id=<?php $fiche->infos('id'); ?>',
				data: { valeur: tag }
			});
		});
		
		$(".add-historique").click(function(){
			$(this).hide();
			$(".ajout-historique").show();
		});
		
		$(".add-element").click(function(){
			var objet = $("#form-objet").val();
			var type = $("#form-type").val();
			var date = $("#form-date").val();
			
			if (objet != '') {
				if (type != '') {
					if (date != '') {
						$.ajax({
							type: 'POST',
							url: 'ajax-form.php?action=ajout-historique&id=<?php $fiche->infos('id'); ?>',
							data: { 'objet': objet, 'type': type, 'date': date },
							dataType: 'html'
						}).done(function(data){
							$("#historique-contact tbody tr:first").after(data);
						});
					}
				}
			}
			
			// On vide le formulaire
			$("#form-objet").val('');
			$("#form-date").val('');
			$("#form-type").val('contact');
			$(".ajout-historique").hide();
			$(".add-historique").show();
			
			return true;
		});
		
		$("#form-objet").keypress(function(e){
			if (e.which == 13) { // touche entrée appuyée ou tab
				var objet = $("#form-objet").val();
				var type = $("#form-type").val();
				var date = $("#form-date").val();
				
				if (objet != '') {
					if (type != '') {
						if (date != '') {
							$.ajax({
								type: 'POST',
								url: 'ajax-form.php?action=ajout-historique&id=<?php $fiche->infos('id'); ?>',
								data: { 'objet': objet, 'type': type, 'date': date },
								dataType: 'html'
							}).done(function(data){
								$("#historique-contact tbody tr:first").after(data);
							});
						}
					}
				}
				
				// On vide le formulaire
				$("#form-objet").val('');
				$("#form-date").val('');
				$("#form-type").val('contact');
				$(".ajout-historique").hide();
				$(".add-historique").show();
				
				return true;
			}
		});
		
		
		// Script de suppression d'une tache
		$(".fin-tache").click(function(){
			// On récupère l'ID de la tâche
			var tache_id = $(this).data('tache');
			
			// On appelle la fonction AJAX pour l'envoi
			$.ajax({
				type: 'POST',
				url: 'ajax-form.php?action=retrait-tache',
				data: { 'tache' : tache_id, 'fiche' : <?php $fiche->infos('id'); ?> },
				dataType: 'html'
			});
			
			// On retire de l'affichage la tâche en question
			$("#tache-" + tache_id).fadeOut('slow');
		});		
	});
</script>

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
	var GeocoderOptions = { 'address': "<?php $fiche->affichage_adresse(''); ?>", 'region': 'FR' };
	
	// La function qui va traiter le résultat
	function GeocodingResult(results, status) {
		// Si la recherche a fonctionnée
		if (status == google.maps.GeocoderStatus.OK) {
			// On créé un nouveau marker sur la map
			markerAdresse = new google.maps.Marker({
				position: results[0].geometry.location,
				map: map,
				title: "<?php $fiche->affichage_nom(); ?>"
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