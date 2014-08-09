<?php $core->tpl_header(); ?>

<section id="fiche-electeur" data-fiche="<?php $fiche->the_ID(); ?>">
	<header>
		<h2><?php $fiche->affichage_nom('span'); ?></h2>
	
		<div id="electeur-icons">
			<?php if ($fiche->get_infos('electeur')) : ?><span id="est-electeur" title="Électeur">&#xe840;</span><?php endif; ?>
		</div>
	</header>
	
	<div id="carte" data-nom="<?php $fiche->affichage_nom(); ?>" data-adresse="<?php $fiche->affichage_adresse(' '); ?>"></div>

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
			<label class="formulaire">Adresse email</label>
			<input class="fiche" type="email" name="email" id="form-email" placeholder="abc@domaine.fr" value="<?php $fiche->contact('email'); ?>">
			<span id="valider-form-email">Valider</span>
			<span id="reussite-form-email">&#xe812;</span>
		</li>
		<li>
			<label class="formulaire">Téléphone portable</label>
			<input class='fiche' type='text' name='mobile' id='form-mobile' placeholder='00 00 00 00 00' value="<?php $core->tpl_phone($fiche->contact('mobile', false, true)); ?>">
			<span id="valider-form-mobile">Valider</span>
			<span id="reussite-form-mobile">&#xe812;</span>
		</li>
		<li>
			<label class="formulaire">Téléphone fixe</label>
			<input class='fiche' type='text' name='telephone' id='form-telephone' placeholder='00 00 00 00 00' value="<?php $core->tpl_phone($fiche->contact('telephone', false, true)); ?>">
			<span id="valider-form-telephone">Valider</span>
			<span id="reussite-form-telephone">&#xe812;</span>
		</li>
	</ul>
	
	<ul>
		<li id="modifierAdressePostale">Changer l'adresse postale</li>
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

			// On charge d'abord le volet de l'historique des événements
			$core->tpl_load('aside', 'historique');
			
			// On charge le script de chargement d'un nouveau fichier
			$core->tpl_load('aside', 'fichier-nouveau');

		endif;
		
		// On charge ici les volets qui peuvent être appelés quelque soit la présence ou non d'un historique
		
			// On charge le script de changement d'adresse
			$core->tpl_load('aside', 'changement-adresse');
		
		
		// On charge ici les volets qui ne seront appelés que lors des appels javascript
		$core->tpl_load( 'aside' , 'nouvelleinteraction' ); // Appelé pour créer une nouvelle fiche dans l'historique
		
		
		// On prépare ici la liste des div qui seront créé vides pour être remplis par des scripts AJAX
		$divs = array('interaction', 'volet');
		
		// On affiche les div en question
		foreach ($divs as $div) { echo '<div id="' . $div . '"></div>'; }
	?>
</aside>

<script>
	$(document).ready(function() {	
	
		// javascript relatif à la partie Fichier
		
			//$("#form-mobile").inputmask("99 99 99 99 99");
			//$("#form-telephone").inputmask("99 99 99 99 99");
			//$("#form-date").inputmask("9{1,2}/9{1,2}/9{2,4}");
					
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
		
				
	});
</script>

<?php $core->tpl_footer(); ?>