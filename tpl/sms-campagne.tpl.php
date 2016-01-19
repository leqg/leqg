<?php
    // On met en place la protection
    User::protection(5);
    
    // On récupère les informations sur la campagne demandée
    $campagne = new Campagne($_GET['campagne']);

    // On charge le template
    Core::tpl_header(); 
?>
	
	<h2 class="titreCampagne" data-campagne="<?php echo $campagne->get('code'); ?>" data-page="campagne"><?php echo $campagne->get('campagne_titre');?></h2>
	
	<div class="colonne demi gauche">
		<section class="contenu demi">
				<h4>SMS envoyé</h4>
			
			<p><em><?php echo $campagne->get('campagne_message'); ?></em></p>
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
					<span><?php echo User::get_login_by_id($campagne->get('campagne_createur')); ?></span>
				</li>
				<li class="prix">
					<span>Coût de la campagne</span>
					<span><strong><?php echo number_format($campagne->get('prix'), 2, ',', ' '); ?>&nbsp;&euro;</strong> pour <?php echo number_format($campagne->get('nombre'), 0, ',', ' '); ?> envoi<?php if ($campagne->get('nombre') > 1) { ?>s<?php 
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

<?php Core::tpl_footer(); ?>