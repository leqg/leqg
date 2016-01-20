<?php
    // On protège la page
    User::protection(5);
    
    // On charge le contact
if (isset($_GET['fiche'])) :
    $contact = new Contact(md5($_GET['fiche']));
    else :
        Core::goTo('contacts', true);
    endif;
    
    // On charge le template
    Core::loadHeader();
?>
<section id="fiche">
	<header>
    <?php if (!empty($contact->get('contact_nom')) || !empty($contact->get('contact_nom_usage')) || !empty($contact->get('contact_prenoms'))) : ?>
		<h2><?php echo $contact->get('nom_affichage'); ?></h2>
    <?php elseif (!empty($contact->get('contact_organisme'))) : ?>
		<h2><?php echo $contact->get('contact_organisme'); ?>
    <?php else : ?>
		<h2>Fiche sans nom</h2>
    <?php endif; ?>
	</header>
	
	<ul class="infos">
    <?php if ($contact->get('contact_naissance_date') != '0000-00-00') : ?>
		<li class="age">
			<p>Né le <?php echo date('d/m/Y', strtotime($contact->get('contact_naissance_date'))); ?> (<?php echo $contact->age(); ?>)</p>
		</li>
    <?php endif; ?>
		<li class="adresse">
    <?php 
    if ($contact->get('adresse_id')) :
        $adresse = Carto::detailAdresse($contact->get('adresse_id'));
        echo $adresse['immeuble_numero'] . ' ' . mb_convert_case(trim($adresse['rue_nom']), MB_CASE_TITLE) . '<br>' . $adresse['code_postal'] . ' ' . mb_convert_case($adresse['commune_nom'], MB_CASE_UPPER);
                elseif ($contact->get('immeuble_id')) :
                    $adresse = Carto::detailAdresse($contact->get('immeuble_id'));
                    echo $adresse['immeuble_numero'] . ' ' . mb_convert_case(trim($adresse['rue_nom']), MB_CASE_TITLE) . '<br>' . $adresse['code_postal'] . ' ' . mb_convert_case($adresse['commune_nom'], MB_CASE_UPPER);
                else :
                    echo 'Aucune adresse renseignée';
                endif;
    ?>
		</li>
    <?php if ($contact->get('contact_email')) : ?>
		<li class="email">
    <?php echo $contact->get('email'); ?>
		</li>
    <?php endif; ?>
    <?php if ($contact->get('contact_mobile')) : ?>
		<li class="mobile">
    <?php echo $contact->get('mobile'); ?>
		</li>
    <?php endif; ?>
    <?php if ($contact->get('contact_fixe')) : ?>
		<li class="fixe">
    <?php echo $contact->get('fixe'); ?>
		</li>
    <?php endif; ?>
	</ul>
</section>

<section id="historique">
	<div id="scroll">
		<h2>Historique des interactions</h2>
		
		<ul class="infos">
    <?php $interactions = $contact->listeEvenements(); foreach ($interactions as $event) : $interaction = new Evenement($event[0], false); ?>
			<li>
				<p>
					<em><?php echo date('d/m/Y', strtotime($interaction->get('historique_date'))); ?></em><br>
					
					<em><?php Core::eventType($interaction->get('historique_type')); ?></em><br>
					
        <?php if ($interaction->get('historique_type') == 'sms') : ?><strong>Envoi d'un SMS</strong><br><em>&laquo;&nbsp;<?php echo $interaction->get('historique_notes'); ?>&nbsp;&raquo;</em>
        <?php elseif ($interaction->get('historique_type') == 'porte') : ?><strong>Porte à porte</strong><br><em><?php echo $interaction->get('historique_objet'); ?></em>
        <?php else : ?><strong><?php echo $interaction->get('historique_objet'); ?></strong>
        <?php endif; ?>
				</p>
			</li>
    <?php endforeach; ?>
		</ul>
	</div>
	
	<nav id="actions-fiche">
		<a href="#" id="retourDepuisHistorique" class="retour">&#xe813;</a>
	</nav>
</section>

<section id="modification">
	<h2>Modification de la fiche</h2>
	
	<form action="ajax.php?script=fiche-modification" method="post">
		<input type="hidden" name="fiche" value="<?php echo $contact->get('contact_id'); ?>">
		<ul class="formulaire">
			<li>
				<label for="form-email">Email</label>
				<input type="email" name="email" id="form-email" value="<?php echo $contact->get('email'); ?>">
			</li>
			<li>
				<label for="form-fixe">Phone</label>
				<input type="tel" name="mobile" id="form-mobile" value="<?php echo $contact->get('mobile'); ?>">
			</li>
			<li>
				<label for="form-fixe">Téléphone</label>
				<input type="tel" name="fixe" id="form-fixe" value="<?php echo $contact->get('fixe'); ?>">
			</li>
			<li>
				<input type="submit" value="Enregistrer">
			</li>
		</ul>
	</form>
	
	<nav id="actions-fiche">
		<a href="#" id="retourDepuisModif" class="retour">&#xe813;</a>
	</nav>
</section>

<nav id="actions-fiche">
	<a href="#" id="goToHistorique" class="historique">&#xe8dd;</a>
	<a href="<?php Core::goTo('interaction', array('action' => 'ajout', 'fiche' => $contact->get('contact_id'))); ?>" id="ajoutInteraction" class="central">&#xe816;</a>
	<a href="#" id="goToModif" class="modifier">&#xe855;</a>
</nav>
<?php Core::loadFooter(); ?>