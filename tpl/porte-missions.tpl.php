<?php
	Core::tpl_header();
?>
	<h2 class="titre" data-user="<?php echo User::ID(); ?>">Porte à porte</h2>
	<?php if (Porte::nombre() > 0) : ?>
		<section id="missions">
			<ul class="liste-missions">
				<?php $missions = Porte::missions(); foreach ($missions as $mission) : ?>
				<li>
					<?php if (Porte::estInscrit($mission['mission_id'])) { ?><a href="<?php Core::tpl_go_to('porte', array('action' => 'voir', 'mission' => $mission['mission_id'])); ?>" class="nostyle"><?php } ?><button class="<?php if (!Porte::estInscrit($mission['mission_id'])) { ?>inscription<?php } ?> mission-<?php echo $mission['mission_id']; ?> vert" style="float: right; margin-top: 1.33em;" data-mission="<?php echo $mission['mission_id']; ?>"><?php if (Porte::estInscrit($mission['mission_id'])) { ?>Voir la mission<?php } else { ?>S'inscrire<?php } ?></button><?php if (Porte::estInscrit($mission['mission_id'])) { ?></a><?php } ?>
					<a href="<?php Core::tpl_go_to('porte', array('mission' => md5($mission['mission_id']))); ?>" class="nostyle"><h4><?php echo $mission['mission_nom']; ?></h4></a>
					<p>
						Cette mission de porte-à-porte concerne encore <strong><?php echo Porte::estimation($mission['mission_id']); ?></strong> électeurs.<br>
					</p>
				</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php else : ?>
		<section class="icone" id="aucuneMission">
			<h3>Aucune mission lancée actuellement !</h3>
		</section>
	<?php endif; ?>
	
<?php Core::tpl_footer(); ?>