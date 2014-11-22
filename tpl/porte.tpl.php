<?php
	User::protection(5);
	Core::tpl_header();
?>
	
	<h2>Porte à porte</h2>
	<?php if (Porte::nombre() > 0) : ?>
		<section id="missions">
			<ul class="liste-missions">
				<?php $missions = Porte::missions(); foreach ($missions as $mission) : ?>
				<li>
					<a href="<?php Core::tpl_go_to('porte', array('mission' => md5($mission['mission_id']))); ?>" class="nostyle"><h4><?php echo $mission['mission_nom']; ?></h4></a>
					<p>
						Cette mission de porte-à-porte concerne encore <strong><?php echo Porte::estimation($mission['mission_id']); ?></strong> électeurs.<br>
						<?php if (is_null($mission['mission_deadline'])) { ?>
						Cette mission n'a pas de date limite connue.
						<?php } else { ?>
						Cette mission doit être terminée pour le <strong><?php echo date('d/m/Y', strtotime($mission['mission_deadline'])); ?></strong>.
						<?php } ?>
					</p>
				</li>
				<?php endforeach; ?>
			</ul>
			<a class="nostyle" href="<?php Core::tpl_go_to('porte', array('action' => 'nouveau')); ?>"><button>Créer une nouvelle mission</button></a>
		</section>
	<?php else : ?>
		<section class="icone" id="aucuneMission">
			<h3>Aucune mission lancée actuellement !</h3>
			<a class="nostyle" href="<?php Core::tpl_go_to('porte', array('action' => 'nouveau')); ?>"><button>Créer une mission</button></a>
		</section>
	<?php endif; ?>
	
<?php Core::tpl_footer(); ?>