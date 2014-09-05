<?php $core->tpl_header(); ?>

<section id="fiche" data-fiche="<?php $fiche->the_ID(); ?>">
	<header>
		<h2><?php $fiche->affichage_nom('span'); ?></h2>
		<a href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id(), 'modifierInformations' => 'true')); ?>" class="nostyle" id="config-icon">&#xe855;</a>
	</header>

	<nav class="actions">
		<?php if (!empty($fiche->get_infos('mobile'))) : ?>
			<a class="nostyle icone carre positif" title="Envoyer un SMS" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_ID(), 'envoyerSMS' => 'true')); ?>">&#xe8e4;<span class="legende">Envoyer&nbsp;un&nbsp;SMS</span></a>
		<?php endif; ?>
		<?php if (!empty($fiche->get_infos('email'))) : ?>
			<a class="nostyle icone carre positif" title="Envoyer un Email" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_ID(), 'envoyerEmail' => 'true')); ?>">&#xe805;<span class="legende">Envoyer&nbsp;un&nbsp;email</span></a>
		<?php endif; ?>
	</nav>
	
	<div id="carte" data-nom="<?php $fiche->affichage_nom(); ?>" data-adresse="<?php $carto->adressePostale($fiche->get_immeuble(), ' '); ?>"></div>

	<form action="#" method="post">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Sexe</span>
				<p><?php $fiche->sexe(); ?></p>
				<span class="icone-modification" id="modifierSexe" title="Modifier le sexe pour le genre opposé">&#xe839;</span>
			</li>
			<li>
				<span class="label-information">Date de naissance</span>
				<p><?php if ($fiche->is_info('naissance_date')) : ?>
					<?php $fiche->date_naissance(' / '); ?> <?php $fiche->lieu_de_naissance('à'); ?><br>
					<?php $fiche->age(); ?>
				<?php else : ?>
					Date de naissance inconnue
				<?php endif; ?></p>
				<a class="nostyle icone" title="Modifier les informations de naissance" href="<?php $core->tpl_go_to('fiche', array('id' => $_GET['id'], 'changementNaissance' => 'true')); ?>">&#xe855;</a>
			</li>
			<li>
				<span class="label-information">Adresse déclarée</span>
				<?php if ($fiche->get_adresse()) : ?>
				<p class="adresse"><?php $carto->adressePostale($fiche->get_adresse()); ?></p>
				<?php else : ?>
				<p>Aucune adresse connue</p>
				<?php endif; ?>
				<a class="nostyle icone" title="Modifier l'adresse déclarée" href="<?php $core->tpl_go_to('fiche', array('id' => $_GET['id'], 'modifierAdresse' => 'true')); ?>">&#xe855;</a>
			</li>
			<?php if ($fiche->get_immeuble()) : ?>
			<li>
				<span class="label-information">Fichier électoral</span>
				<p class="adresse"><?php $carto->adressePostale($fiche->get_immeuble()); ?>&nbsp;</p>
			</li>
			<?php endif; ?>
			<?php if ($fiche->get_immeuble() && $carto->bureauParImmeuble($fiche->get_immeuble()) != 0) : ?>
			<li>
				<span class="label-information">Bureau de vote</span>
				<p><?php $carto->bureauDeVote($fiche->get_immeuble()); ?></p>
				<a class="nostyle icone" title="Emplacement du bureau de vote" href="<?php $core->tpl_go_to('carto', array('module' => 'bureaux', 'bureau' => $carto->bureauParImmeuble($fiche->get_immeuble()))); ?>">&#xe844;</a>
			</li>
			<?php endif; ?>
			<li>
				<span class="label-information"><label for="form-email">Adresse email</label></span>
				<input class="fiche" type="email" name="email" id="form-email" placeholder="abc@domaine.fr" value="<?php $fiche->contact('email'); ?>">
				<span id="valider-form-email">Valider</span>
				<span id="reussite-form-email">&#xe812;</span>
				<span id="sauvegarde-form-email">&#xe917;</span>
			</li>
			<li>
				<span class="label-information"><label for="form-mobile">Téléphone mobile</label></span>
				<input class='fiche' type='text' name='mobile' id='form-mobile' placeholder='00 00 00 00 00' value="<?php $core->tpl_phone($fiche->contact('mobile', false, true)); ?>">
				<span id="valider-form-mobile">Valider</span>
				<span id="reussite-form-mobile">&#xe812;</span>
				<span id="sauvegarde-form-mobile">&#xe917;</span>
			</li>
			<li>
				<span class="label-information"><label for="form-telephone">Téléphone fixe</label></span>
				<input class='fiche' type='text' name='telephone' id='form-telephone' placeholder='00 00 00 00 00' value="<?php $core->tpl_phone($fiche->contact('telephone', false, true)); ?>">
				<span id="valider-form-telephone">Valider</span>
				<span id="reussite-form-telephone">&#xe812;</span>
				<span id="sauvegarde-form-telephone">&#xe917;</span>
			</li>
			<li>
				<span class="label-information">Tags</span>
				<p class="listeTags"><?php $fiche->tags(); ?><span class="tag ajoutTag">+</span></p>
			</li>
		</ul>
	</form>
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

			// On charge d'abord le volet de l'historique des événements
			$core->tpl_load('aside', 'liste-dossiers');
			
			// On charge le script de chargement d'un nouveau fichier
			$core->tpl_load('aside', 'fichier-nouveau');
			
			// On charge le script de modifier de l'interaction
			$core->tpl_load('aside', 'modifier-fiche');
			
			// On charge le script de liaison d'un événement à un dossier
			$core->tpl_load('aside', 'lier-un-dossier');
			
			// On charge le script de liaison d'un événement à un dossier
			$core->tpl_load('aside', 'creer-un-dossier');
			
			// On charge le script de liaison d'un événement à un dossier
			$core->tpl_load('aside', 'ajout-tache');

		endif;
		
		// On charge ici les volets qui peuvent être appelés quelque soit la présence ou non d'un historique
		
			// On charge ici les volets qui ne seront appelés que lors des appels javascript
			$core->tpl_load('aside', 'nouvelleinteraction');
			
			// On charge le script de changement des informations de naissance
			$core->tpl_load('aside', 'changement-naissance');
			
			// On charge le script de changement des informations de naissance
			$core->tpl_load('aside', 'changement-information');
			
			// On charge le script de changement des informations de naissance
			$core->tpl_load('aside', 'envoi-sms');
			
			// On charge le script de changement des informations de naissance
			$core->tpl_load('aside', 'envoi-email');
		
		
		// On charge les informations de changement d'adresse, si c'est demandé
			if (isset($_GET['modifierAdresse']) || isset($_GET['modifierRue']) || isset($_GET['modifierImmeuble']) || isset($_GET['creerImmeuble'])) :
				// On charge le script de changement d'adresse
				$core->tpl_load('aside', 'changement-adresse');
			endif;
		
		
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
					
					// On met en place un affichage d'attente
					$("#valider-form-email").fadeOut('slow');
					$("#sauvegarde-form-email").fadeIn('slow');
				
					// On appelle l'AJAX
					$.ajax({
						type: 'POST',
						url: 'ajax-form.php?action=maj-fiche&id=<?php $fiche->infos('id'); ?>',
						data: { champ: 'email', valeur: value },
						dataType: 'html'
					}).done(function(){
						$("#sauvegarde-form-email").fadeOut('slow');
						$("#reussite-form-email").fadeIn('slow').delay(1500).fadeOut('slow');
					});
				});
				
				$("#form-telephone").change(function() {
					// On récupère les informations
					var value = $(this).val();
					
					// On met en place un affichage d'attente
					$("#valider-form-telephone").fadeOut('slow');
					$("#sauvegarde-form-telephone").fadeIn('slow');
				
					// On appelle l'AJAX
					$.ajax({
						type: 'POST',
						url: 'ajax-form.php?action=maj-fiche&id=<?php $fiche->infos('id'); ?>',
						data: { champ: 'telephone', valeur: value },
						dataType: 'html'
					}).done(function(){
						$("#sauvegarde-form-telephone").fadeOut('slow');
						$("#reussite-form-telephone").fadeIn('slow').delay(1500).fadeOut('slow');
					});
				});
				
				$("#form-mobile").change(function() {
					// On récupère les informations
					var value = $(this).val();
					
					// On met en place un affichage d'attente
					$("#valider-form-mobile").fadeOut('slow');
					$("#sauvegarde-form-mobile").fadeIn('slow');
				
					// On appelle l'AJAX
					$.ajax({
						type: 'POST',
						url: 'ajax-form.php?action=maj-fiche&id=<?php $fiche->infos('id'); ?>',
						data: { champ: 'mobile', valeur: value },
					}).done(function(){
						$("#sauvegarde-form-mobile").fadeOut('slow');
						$("#reussite-form-mobile").fadeIn('slow').delay(1500).fadeOut('slow');
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