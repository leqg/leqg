<?php
	// On protège la page
	User::protection(5);

	// On ouvre la mission
	$data = new Mission($_GET['code']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::goTo('porte', true);
	
	// On récupère la liste des contacts avec procu ou recontact
    $procurations = array();
    $recontacts = array();
	
	// typologie
	$typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
	Core::loadHeader();
?>
<a href="<?php Core::goTo($typologie); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Revenir à la liste</button></a>	
<h2 id="titre-mission" class="titre" data-mission="<?php echo $data->get('mission_hash'); ?>">Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

<nav class="onglets">
    <a href="<?php Core::goTo('mission', array('code' => $data->get('mission_hash'))); ?>">Supervision</a>
    <a href="<?php Core::goTo('mission', array('code' => $data->get('mission_hash'), 'admin' => 'militant')); ?>">Militants</a>
    <a href="<?php Core::goTo('mission', array('code' => $data->get('mission_hash'), 'admin' => 'parcours')); ?>">Parcours</a>
    <?php if ($data->get('mission_type') == 'porte') { ?><a href="<?php Core::goTo('mission', array('code' => $data->get('mission_hash'), 'admin' => 'retours')); ?>">Demandes</a><?php } ?>
</nav>

<div class="colonne demi gauche">
    <section class="contenu demi">
        <h4>Électeurs souhaitant être recontactés</h4>
		
		<ul class="listeContacts">
    	<?php
            if ($data->nombre_recontacts()) :
				// On fait la liste des contacts concernés
				$contacts = $data->liste_contacts(4);
				foreach ($contacts as $contact) :
					// On ouvre la fiche du contact concerné
					$fiche = new People($contact[0]);
					
					if ($fiche->get('sexe') == 'H') { $sexe = 'homme'; }
					elseif ($fiche->get('sexe') == 'F') { $sexe = 'femme'; }
					else { $sexe = 'isexe'; }
					
					if (!empty($fiche->display_name())) { $nomAffichage = $fiche->display_name(); }
					elseif (!empty($fiche->get('organisme'))) { $nomAffichage = $fiche->get('organisme'); }
					else { $nomAffichage = 'Fiche sans nom'; }
        ?>
		<a href="<?php Core::goTo('contact', array('contact' => $fiche->get('id'))); ?>" class="nostyle contact-<?php echo $fiche->get('id'); ?>">
			<li class="contact <?php echo $sexe; ?>">
				<strong><?php echo $nomAffichage; ?></strong>
			</li>
		</a>
    	<?php endforeach; else : ?>
    	<li class="contact isexe icoclair">
    	    <strong>Aucun électeur à contacter</strong>
    	</li>
    	<?php endif; ?>
		</ul>
    </section>
</div>

<div class="colonne demi droite">
    <section class="contenu demi">
        <h4>Électeurs souhaitant donner une procuration</h4>
		
		<ul class="listeContacts">
    	<?php
            if ($data->nombre_procurations()) :
				// On fait la liste des contacts concernés
				$contacts = $data->liste_contacts(3);
				foreach ($contacts as $contact) :
					// On ouvre la fiche du contact concerné
					$fiche = new Contact(md5($contact[0]));
					
					if ($fiche->get('sexe') == 'H') { $sexe = 'homme'; }
					elseif ($fiche->get('sexe') == 'F') { $sexe = 'femme'; }
					else { $sexe = 'isexe'; }
					
					if (!empty($fiche->display_name())) { $nomAffichage = $fiche->display_name(); }
					elseif (!empty($fiche->get('organisme'))) { $nomAffichage = $fiche->get('organisme'); }
					else { $nomAffichage = 'Fiche sans nom'; }
        ?>
		<a href="<?php Core::goTo('contact', array('contact' => $fiche->get('id'))); ?>" class="nostyle contact-<?php echo $fiche->get('id'); ?>">
			<li class="contact <?php echo $sexe; ?>">
				<strong><?php echo $nomAffichage; ?></strong>
			</li>
		</a>
    	<?php endforeach; else : ?>
    	<li class="contact isexe icoclair">
    	    <strong>Aucune procuration trouvée</strong>
    	</li>
    	<?php endif; ?>
		</ul>
    </section>
</div>