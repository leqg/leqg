<?php
	// On se connecte à la BDD
	$link = Configuration::read('db.link');
	$userId = User::ID();
	
	// On regarde si un appel est ouvert
	$query = $link->prepare('SELECT * FROM `rappels` WHERE `user_id` = :user AND `rappel_statut` = 1');
	$query->bindParam(':user', $userId, PDO::PARAM_INT);
	$query->execute();
	
	// On regarde si un appel existe
	if ($query->rowCount()) {
		$rappel = $query->fetch(PDO::FETCH_ASSOC);
		
		// On ouvre l'argumentaire
		$query = $link->prepare('SELECT * FROM `argumentaires` WHERE `argumentaire_id` = :argumentaire');
		$query->bindParam(':argumentaire', $rappel['argumentaire_id'], PDO::PARAM_INT);
		$query->execute();
		$argumentaire = $query->fetch(PDO::FETCH_ASSOC);
		
		// On ouvre la fiche du contact
		$contact = new Contact(md5($rappel['contact_id']));
		
		// On cherche le nom à afficher
		if (!empty($contact->get('nom_affichage'))) { $nomAffichage = $contact->get('nom_affichage'); }
		elseif (!empty($contact->get('contact_organisme'))) { $nomAffichage = $contact->get('contact_organisme'); }
		else { $nomAffichage = 'Fiche sans nom'; }
	}
	
	// Sinon, on en ouvre un, au hasard
	else {
		$query = $link->query('SELECT * FROM `rappels` WHERE `user_id` = 0 AND `rappel_statut` = 0 ORDER BY rand() LIMIT 0, 1');
		$rappel = $query->fetch(PDO::FETCH_ASSOC);
		
		// On affecte ce rappel à l'utilisateur qui vient de l'ouvrir
		$query = $link->prepare('UPDATE `rappels` SET `user_id` = :user, `rappel_statut` = 1 WHERE `argumentaire_id` = :argumentaire AND `contact_id` = :contact');
		$query->bindParam(':user', $userId, PDO::PARAM_INT);
		$query->bindParam(':argumentaire', $rappel['argumentaire_id'], PDO::PARAM_INT);
		$query->bindParam(':contact', $rappel['contact_id'], PDO::PARAM_INT);
		$query->execute();
		
		// On ouvre l'argumentaire
		$query = $link->prepare('SELECT * FROM `argumentaires` WHERE `argumentaire_id` = :argumentaire');
		$query->bindParam(':argumentaire', $rappel['argumentaire_id'], PDO::PARAM_INT);
		$query->execute();
		$argumentaire = $query->fetch(PDO::FETCH_ASSOC);
		
		// On ouvre la fiche du contact
		$contact = new Contact(md5($rappel['contact_id']));
		
		// On cherche le nom à afficher
		if (!empty($contact->get('nom_affichage'))) { $nomAffichage = $contact->get('nom_affichage'); }
		elseif (!empty($contact->get('contact_organisme'))) { $nomAffichage = $contact->get('contact_organisme'); }
		else { $nomAffichage = 'Fiche sans nom'; }
	}
	
	// On charge le template
	Core::tpl_header();
?>
	<h2 class="titre" data-argumentaire="<?php echo $argumentaire['argumentaire_id']; ?>" data-contact="<?php echo $contact->get('contact_id'); ?>">Appel militant</h2>
	
	<div class="colonne demi gauche">
		<section class="contenu demi">
			<h4><?php echo $nomAffichage; ?></h4>
			
			<?php if ($contact->get('bureau_id') && $contact->get('immeuble_id')) { ?>
			<ul class="etatcivil">
				<li class="bureau"><?php echo $contact->bureau(); ?></li>
				<li class="immeuble"><?php echo $contact->adresse('electorale'); ?></li>
			</ul>
			<?php } else { ?>
			<ul class="etatcivil">
				<li class="bureau">Le contact ne semble pas électeur.</li>
			</ul>
			<?php } ?>
			
			<h4>Données de contact</h4>
			<ul class="etatcivil coordonnees">
				<?php $coordonnees = $contact->coordonnees(); foreach ($coordonnees as $coordonnee) : if ($coordonnee['coordonnee_type'] != 'email') : ?>
				<li class="<?php echo $coordonnee['coordonnee_type']; ?>" id="<?php echo $coordonnee['coordonnee_type']; ?>-<?php echo $coordonnee['coordonnee_id']; ?>" data-id="<?php echo $coordonnee['coordonnee_id']; ?>"><?php 
					Core::tpl_phone($coordonnee['coordonnee_numero']); 
			  ?></li>
				<?php endif; endforeach; ?>
			</ul>
		</section>
		
		<section class="contenu demi">
			<h4>Remarques concernant l'appel</h4>
			
			<ul class="formulaire">
				<li>
					<span class="form-icon decalage notes">
						<textarea class="long" name="reporting" id="reporting"><?php echo $rappel['rappel_reporting']; ?></textarea>
					</span>
				</li>
			</ul>
			
			<button class="appelSuivant" style="margin-bottom: .5em;">Aller à l'appel suivant</button>
		</section>
	</div>
	
	<div class="colonne demi droite">
		<section class="contenu demi">
			<h4>Argumentaire – fil conducteur de l'appel</h4>
			
			<?php if (!empty($argumentaire['argumentaire_texte'])) : ?>
			<p><?php echo nl2br($argumentaire['argumentaire_texte']); ?></p>
			<?php else : ?>
			<p><em>Aucun argumentaire ajouté par l'équipe.</em></p>
			<?php endif; ?>
		</section>
	</div>
<?php Core::tpl_footer(); ?>