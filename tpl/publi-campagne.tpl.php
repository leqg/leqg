<?php
    // On met en place la protection
    User::protection(5);
    
    // On récupère les informations sur la campagne demandée
    $campaign = new Campaign($_GET['campagne']);

    // On charge le template
    Core::loadHeader(); 
?>
	
	<h2 class="titreCampagne" data-campagne="<?php echo $campaign->get('id'); ?>" data-page="campagne"><?php if (!empty($campaign->get('titre'))) { echo $campaign->get('titre'); 
} else { echo 'Campagne sans titre'; 
}?></h2>
	
	<div class="colonne demi gauche">
		<section class="contenu demi">
			<h4>Informations annexes</h4>
			
			<ul class="informations">
				<li class="fichier">
					<span>Fichier de publipostage</span>
					<span><a href="exports/publi-<?php echo md5($campaign->get('id')); ?>.csv" class="nostyle"><strong>Ouvrir le fichier</strong></a></span>
				</li>
				<li class="date">
					<span>Date d'envoi</span>
					<span><strong><?php echo strftime('%d %B %Y', strtotime($campaign->get('date'))); ?></strong></span>
				</li>
				<li class="utilisateur">
					<span>Utilisateur à l'origine de la campagne</span>
					<span><?php echo User::get_login_by_id($campaign->get('user')); ?></span>
				</li>
				<li class="email">
					<span>Nombre d'envois</span>
					<span><strong><?php echo number_format($campaign->get('count'), 0, ',', ' '); ?></strong> envoi<?php if ($campaign->get('count') > 1) { ?>s<?php 
    } ?></span>
				</li>
			</ul>
		</section>
	</div>
	
	<div class="colonne demi droite">
		<section class="contenu demi">
			<h4>Description de la campagne</h4>
			
			<p><?php echo nl2br($campaign->get('message')); ?></p>
		</section>
		
		<section class="contenu demi">
			<h4>Liste des contacts concernés</h4>
			
			<ul class="listeContacts"></ul>
		</section>
	</div>

<?php Core::loadFooter(); ?>