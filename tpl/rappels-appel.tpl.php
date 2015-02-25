<?php
	// On met en place la protection
	User::protection(1);

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
		$contact = new People($rappel['contact_id']);
		
		// On cherche le nom à afficher
		if (!empty($contact->display_name())) { $nomAffichage = $contact->display_name(); }
		elseif (!empty($contact->get('organisme'))) { $nomAffichage = $contact->get('organisme'); }
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
		$contact = new People($rappel['contact_id']);
		
		// On cherche le nom à afficher
		if (!empty($contact->display_name())) { $nomAffichage = $contact->display_name(); }
		elseif (!empty($contact->get('organisme'))) { $nomAffichage = $contact->get('organisme'); }
		else { $nomAffichage = 'Fiche sans nom'; }
	}
	
	// On charge le template
	Core::tpl_header();
?>
	<h2 class="titre" data-argumentaire="<?php echo $argumentaire['argumentaire_id']; ?>" data-contact="<?php echo $contact->get('id'); ?>">Appel militant</h2>
	
	<div class="colonne demi gauche">
		<section class="contenu demi">
			<h4><?php echo $nomAffichage; ?></h4>
			<?php $address = $contact->postal_address(); ?>
			<?php if ($contact->get('bureau') && !empty($address['officiel'])) { ?>
			<ul class="etatcivil">
				<li class="bureau"><?php echo $contact->get('bureau'); ?></li>
				<li class="immeuble"><?php echo $address['officiel']; ?></li>
			</ul>
			<?php } else { ?>
			<ul class="etatcivil">
				<li class="bureau">Le contact ne semble pas électeur.</li>
			</ul>
			<?php } ?>
			
			<h4>Données de contact</h4>
			<ul class="etatcivil coordonnees">
				<?php $coordonnees = $contact->contact_details(); foreach ($coordonnees as $coordonnee) : if ($coordonnee['coordonnee_type'] != 'email') : ?>
				<li class="<?php echo $coordonnee['coordonnee_type']; ?> noUpdate" id="<?php echo $coordonnee['coordonnee_type']; ?>-<?php echo $coordonnee['coordonnee_id']; ?>" data-id="<?php echo $coordonnee['coordonnee_id']; ?>"><?php 
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
		
		<section class="contenu demi">
			<button class="appelSuivant long" data-reporting="2" style="margin-bottom: .5em;">Appel terminé</button>
			<button class="appelSuivant long" data-reporting="3" style="margin-bottom: .5em;">Appel terminé, souhaite donner procuration</button>
			<button class="appelSuivant long" data-reporting="4" style="margin-bottom: .5em;">Appel terminé, souhaite être recontacté</button>
			<button class="appelSansReponse long deleting" style="margin-top: 2em; margin-bottom: .5em;">Appel sans réponse</button>
		</section>
	</div>
<?php Core::tpl_footer(); ?>