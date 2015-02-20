<?php
	// On met en place la protection
	User::protection(5);
	
	// On récupère la liste des campagnes
	$liste = Campaign::all('email');

	// On charge le template
	Core::tpl_header(); 
?>
	
	<h2 class="titreCampagne" data-page="campagnes">Campagnes de Email groupés</h2>
	<?php if (count($liste)) : ?>
		<section id="campagnes">
			<ul class="liste-campagnes">
				<?php foreach ($liste as $element) : ?>
				<li>
					<a href="<?php Core::tpl_go_to('campagne', array('id' => $element['id'])); ?>" class="nostyle"><h4><?php if (!empty($element['titre'])) { echo $element['titre']; } else { echo 'Campagne sans titre'; } ?></h4></a>
                    <p>Campagne <?php echo $element['type']; ?> créée le <?php echo date('d/m/Y', strtotime($element['date'])); ?></p>
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