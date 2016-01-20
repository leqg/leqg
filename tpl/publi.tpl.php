<?php
    // On met en place la protection
    User::protection(5);
    
    // On récupère la liste des campagnes
    $liste = Campaign::all('publi');

    // On charge le template
    Core::loadHeader(); 
?>
	<h2 class="titreCampagne" data-page="campagnes">Préparation des campagnes de publipostage</h2>
    <?php if (count($liste)) : ?>
		<section id="campagnes">
			<ul class="liste-campagnes">
				<?php foreach ($liste as $element) : $campaign = new Campaign($element['id']); ?>
				<li>
					<a href="<?php Core::goPage('publi', array('campagne' => $element['id'])); ?>" class="nostyle"><h4><?php if (!empty($element['titre'])) { echo $element['titre']; 
    } else { echo 'Campagne sans titre'; 
} ?></h4></a>
					<p>
						Cette campagne de publipostage a été envoyée à <strong><?php echo number_format($campaign->get('count'), 0, ',', ' '); ?></strong> contact<?php if ($campaign->get('count') >1) { ?>s<?php 
     } ?>.<br>
						Elle a été préparée le <strong><?php echo strftime('%d %B %Y', strtotime($campaign->get('date'))); ?></strong> par <em><?php echo User::getLoginByID($campaign->get('user')); ?></em>.
					</p>
				</li>
				<?php endforeach; ?>
			</ul>
			<a href="index.php?page=contacts" class="nostyle"><button>Lancer un tri pour démarrer une campagne</button></a>
		</section>
    <?php else : ?>
		<section class="icone" id="aucuneMission">
			<h3>Aucune campagne lancée actuellement !</h3>
			<a href="index.php?page=contacts" class="nostyle"><button>Lancer un tri pour démarrer une campagne</button></a>
		</section>
    <?php endif; ?>
	
<?php Core::loadFooter(); ?>