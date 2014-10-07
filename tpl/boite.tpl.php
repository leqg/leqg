<?php $core->tpl_header(); ?>
	
	<h2>Boîtage</h2>
	<?php if ($boitage->nombre() > 0) : ?>
		<section id="missions">
			<ul class="liste-missions">
				<?php $missions = $boitage->missions(); foreach ($missions as $mission) : ?>
				<li>
					<a href="<?php $core->tpl_go_to('boite', array('mission' => md5($mission['mission_id']))); ?>" class="nostyle"><h4><?php echo $mission['mission_nom']; ?></h4></a>
					<p>
						Cette mission de boîtage concerne <strong><?php echo $boitage->estimation($mission['mission_id']); ?></strong> électeurs.<br>
						<?php if (is_null($mission['mission_deadline'])) { ?>
						Cette mission n'a pas de date limite connue.
						<?php } else { ?>
						Cette mission doit être terminée pour le <strong><?php echo date('d/m/Y', strtotime($mission['mission_deadline'])); ?></strong>.
						<?php } ?>
					</p>
				</li>
				<?php endforeach; ?>
			</ul>
			<a class="nostyle" href="<?php $core->tpl_go_to('boite', array('action' => 'nouveau')); ?>"><button>Créer une nouvelle mission</button></a>
		</section>
	<?php else : ?>
		<section class="icone" id="aucuneMission">
			<h3>Aucune mission lancée actuellement !</h3>
			<a class="nostyle" href="<?php $core->tpl_go_to('boite', array('action' => 'nouveau')); ?>"><button>Créer une mission</button></a>
		</section>
	<?php endif; ?>
	
<?php $core->tpl_footer(); ?>