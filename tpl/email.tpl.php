<?php
	// On met en place la protection
	User::protection(5);
	
	// On récupère la liste des campagnes
	$liste = Campagne::liste('email');

	// On charge le template
	Core::tpl_header(); 
?>
	
	<h2 class="titreCampagne" data-page="campagnes">Campagnes de Email groupés</h2>
	<?php if (count($liste)) : ?>
		<section id="missions">
			<ul class="liste-missions">
				<?php foreach ($liste as $element) : $campagne = new Campagne($element['code']); ?>
				<li>
					<a href="<?php Core::tpl_go_to('email', array('campagne' => $element['code'])); ?>" class="nostyle"><h4><?php echo $campagne->get('campagne_titre'); ?></h4></a>
					<p>
						Cette campagne <?php echo $campagne->get('campagne_type'); ?> a été envoyée à <strong><?php echo number_format($campagne->get('nombre'), 0, ',', ' '); ?></strong> contact<?php if ($campagne->get('nombre') >1) { ?>s<?php } ?>.<br>
						Elle a été envoyée le <strong><?php echo strftime('%d %B %Y', strtotime($campagne->get('campagne_date'))); ?></strong> par <em><?php echo User::get_login_by_ID($campagne->get('campagne_createur')); ?></em>.
					</p>
				</li>
				<?php endforeach; ?>
			</ul>
			<a href="index.php?page=contacts" class="nostyle"><button>Lancer une nouvelle campagne</button></a>
		</section>
	<?php else : ?>
		<section class="icone" id="aucuneMission">
			<h3>Aucune campagne lancée actuellement !</h3>
			<a href="index.php?page=contacts" class="nostyle"><button>Lancer une nouvelle campagne</button></a>
		</section>
	<?php endif; ?>
	
<?php Core::tpl_footer(); ?>