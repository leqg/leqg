<?php
    // On met en place la protection
    User::protection(5);
    
    // On récupère les informations sur la campagne demandée
    $campagne = new Campagne($_GET['campagne']);

    // On charge le template
    Core::loadHeader(); 
?>
	
	<h2 class="titreCampagne" data-campagne="<?php echo $campagne->get('code'); ?>" data-page="campagne"><?php echo $campagne->get('campagne_titre');?></h2>
	
	<div class="colonne demi gauche">
		<section class="contenu demi">
			<h4>Email envoyé</h4>
			
			<p><?php echo nl2br($campagne->get('campagne_message')); ?></p>
		</section>
		
		<section class="contenu demi">
			<h4>Informations annexes</h4>
			
			<ul class="informations">
				<li class="date">
					<span>Date d'envoi</span>
					<span><strong><?php echo strftime('%d %B %Y', strtotime($campagne->get('campagne_date'))); ?></strong></span>
				</li>
				<li class="utilisateur">
					<span>Utilisateur à l'origine de la campagne</span>
					<span><?php echo User::getLoginByID($campagne->get('campagne_createur')); ?></span>
				</li>
				<li class="email">
					<span>Nombre d'envois</span>
					<span><strong><?php echo number_format($campagne->get('nombre'), 0, ',', ' '); ?></strong> envoi<?php if ($campagne->get('nombre') > 1) { ?>s<?php 
    } ?></span>
				</li>
			</ul>
		</section>
	</div>
	
	<div class="colonne demi droite">
		<section class="contenu demi">
			<h4>Liste des contacts concernés</h4>
			
			<ul class="listeContacts"></ul>
		</section>
	</div>

<?php Core::loadFooter(); ?>