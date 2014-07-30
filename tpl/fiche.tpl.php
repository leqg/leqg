<?php $core->tpl_header(); ?>

<section id="fiche-electeur">
	<header>
		<h2><?php $fiche->affichage_nom('span'); ?></h2>
	
		<div id="electeur-icons">
			<?php if ($fiche->get_infos('electeur')) : ?><span id="est-electeur" title="Électeur">&#xe840;</span><?php endif; ?>
		</div>
	</header>
	
	<ul class="icons">
		<abbr title="Créer une nouvelle tâche"><li id="bouton-add-tache">&#xe836;</li></abbr>
	</ul>

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

<aside>

	<ul id="navigation-aside"><!--
	 --><li class="nav-aside-go" id="nav-aside-start" data-tab="start">Infos. générales</li><!--
	 --><li class="nav-aside-go" id="nav-aside-contenus-associes" data-tab="taches">Tâches et dossiers</li><!--
	 --><li class="nav-aside-go" id="nav-aside-historique" data-tab="historique">Historique</li><!--
 --></ul>

	<div id="aside-start">

		<section id="carte"></section><!--
		
	 --><section id="tags"><span id="tags-fiche"><?php $fiche->tags('span') ?></span><input list="list-tag" id="ajout-tag" class="ajout-tag" type="text" placeholder="Tag à ajouter (puis entrée)"><span id="add-tag">&#xe816;</span></section><!--
	
 --></div>
 	
 	<div id="aside-contenus-associes">
	 	<?php
			// On recherche s'il existe des tâches par rapport au contact
			$taches_liees = $fiche->taches_liees();
		?><!--
	 --><section id="taches">
		 	<h6>Tâches liées au contact</h6>
	 		<a href="<?php $core->tpl_get_url('creation', 'tache', 'type', $fiche->get_infos('id'), 'id'); ?>" id="ajout-tache">Ajouter une tâche</a>
		 	<ul id="taches-liees">
		 		<?php
			 	if ($taches_liees) {
					if (count($taches_liees) >= 1) {
			 			foreach ($taches_liees as $tache) {
				 			echo '<li id="tache-' . $tache['tache_id'] . '">' . $tache['tache_description'] . ' <a href="' . $core->tpl_return_url('tache', 'suppression', 'action', $_GET['id'] . '-' . $tache['tache_id'], 'id') . '">&#xe812;</a></li>';
			 			}
			 		} else {
				 		echo '<li class="nobefore">Aucune tâche associée au contact actuellement.</li>';
			 		}
			 	} else {
				 	echo '<li class="nobefore">Aucune tâche associée au contact actuellement.</li>';
			 	}
		 		?>
		 	</ul>
	 	</section><!--
	
	 --><section id="dossiers">
	 		<h6>Dossiers liés au contact</h6>
	 		<a href="<?php $core->tpl_get_url('creation', 'dossier', 'type', $fiche->get_infos('id'), 'id'); ?>" id="ajout-dossier">Ajouter un dossier</a>
	 		<?php $dossiers_lies = $fiche->dossiers_lies(); ?>
	 		<ul id="dossiers-lies">
	 			<?php
	 			if ($dossiers_lies) {
	 				if (count($dossiers_lies) >= 1) {
		 				foreach ($dossiers_lies as $dossier) {
			 				?>
			 				<a href="<?php $core->tpl_get_url('dossier', $dossier['dossier_id']); ?>">
				 				<li id="dossier-<?php echo $dossier['dossier_id']; ?>" <?php if (!$dossier['dossier_statut']) { ?>class="dossierFerme"<?php } ?>>
					 				<strong><?php echo stripslashes($dossier['dossier_nom']); ?></strong>
					 				<?php if (strlen($dossier['dossier_description']) > 150) { ?>
					 				<p><?php echo substr(stripslashes($dossier['dossier_description']), 0, 150); ?>&hellip;</p>
					 				<?php } else { ?>
					 				<p><?php echo stripslashes($dossier['dossier_description']); ?></p>
					 				<?php } ?>
				 				</li>
			 				</a>
			 				<?php
		 				}
	 				}
	 			} else {
		 			echo '<li class="dossierAbsent"><strong>Aucun dossier associé au contact actuellement.</strong></li>';
	 			}
	 			?>
	 		</ul>
	 	</section><!--
	 
 --></div>
 
 	<div id="aside-historique"><!--
		
	 --><section id="historique">
			<h6>Historique du contact</h6>
			<!-- Liste de l'historique des contacts avec cette fiche -->
			<table id="historique-contact">
				<thead>
					<tr>
						<th>Type</th>
						<th>Date</th>
						<th>Objet <span class="add-historique">&#xe816;</span></th>
					</tr>
				</thead>
				<tbody>
					<tr class="ajout-historique">
						<td>
							<select name="type" id="form-type">
								<option value="contact">Rencontre</option>
								<option value="téléphone">Appel</option>
								<option value="email">Email</option>
								<option value="courrier">Courrier</option>
							</select>
						</td>
						<td><input type="date" name="date" id="form-date" placeholder="dd/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}"></td>
						<td>
							<input type="text" name="objet" id="form-objet" placeholder="Objet">
							<span class="add-element">&#xe812;</span>
						</td>
					</tr>
					<?php
						$query = 'SELECT * FROM historique WHERE contact_id = ' . $fiche->get_infos('id') . ' ORDER BY historique_date DESC';
						$sql = $db->query($query);
						
						if ($sql->num_rows > 0) {
							while ($row = $sql->fetch_assoc()) {
					?>
						<tr>
							<td><?php echo ucwords(utf8_encode($row['historique_type'])); ?></td>
							<td><?php echo date('d/m/Y', strtotime($row['historique_date'])); ?></td>
							<td><?php echo $row['historique_objet']; ?></td>
						</tr>
					<?php } } else { ?>
						<tr>
							<td colspan="3">Aucun historique avec ce contact.</td>
						</tr>	
					<?php } ?>
				</tbody>
			</table>
	 	</section><!--
	 	
 --></div>

</aside>

<datalist id="list-tag">
	<?php
		$query = 'SELECT tag_nom FROM tags ORDER BY tag_nom ASC';
		$sql = $db->query($query);
		while ($row = $sql->fetch_array()) {
	?>
	<option value="<?php echo utf8_encode($row[0]); ?>">
	<?php } ?>
</datalist>

<script>
	$(document).ready(function() {
		$("#form-mobile").inputmask("99 99 99 99 99");
		$("#form-telephone").inputmask("99 99 99 99 99");
		$("#form-date").inputmask("9{1,2}/9{1,2}/9{2,4}");
		
		// Au chargement, on cache l'historique et les tâches pour n'afficher que la carte et les tags, mise en place des onglets
		$("#aside-start").hide();
		$("#aside-historique").hide();
		// On aout aussi la classe actif à l'élément en cours dans le menu
		$("#nav-aside-contenus-associes").addClass('actif');
		
		// Au chargement, on cache les marqueurs "valider" sur chaque formulaire
		$("#valider-form-telephone").hide();
		$("#valider-form-mobile").hide();
		$("#valider-form-email").hide();
		
		// On met en place le système des onglets
		$(".nav-aside-go").click(function(){
			var selection = $(this).data('tab');
			
			if (selection == 'start') {
				$("#aside-start").show();
				$("#aside-contenus-associes").hide();
				$("#aside-historique").hide();
				$("#nav-aside-contenus-associes").removeClass('actif');
				$("#nav-aside-historique").removeClass('actif');
				$("#nav-aside-start").removeClass('actif').addClass('actif');
				
			} else if (selection == 'taches') {
				$("#aside-start").hide();
				$("#aside-contenus-associes").show();
				$("#aside-historique").hide();
				$("#nav-aside-start").removeClass('actif');
				$("#nav-aside-historique").removeClass('actif');
				$("#nav-aside-contenus-associes").removeClass('actif').addClass('actif');
			} else if (selection == 'historique') {
				$("#aside-start").hide();
				$("#aside-contenus-associes").hide();
				$("#aside-historique").show();
				$("#nav-aside-contenus-associes").removeClass('actif');
				$("#nav-aside-start").removeClass('actif');
				$("#nav-aside-historique").removeClass('actif').addClass('actif');
			}
		});
		
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
		zoom: 16
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