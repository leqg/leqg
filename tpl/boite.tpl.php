<?php 
    // Protection du répertoire
    User::protection(5);
    
    // Chargement du template
    Core::tpl_header();
?>	
<h2>Campagnes de boîtage</h2>
<?php if (Boite::nombre() > 0) : ?>
	<section id="missions">
		<ul class="liste-missions">
			<?php $missions = Boite::missions(); foreach ($missions as $mission) : ?>
			<li>
				<a href="<?php Core::tpl_go_to('boite', array('mission' => md5($mission['mission_id']))); ?>" class="nostyle"><h4><?php echo $mission['mission_nom']; ?></h4></a>
				<p>
					Cette mission de boîtage concerne <strong><?php echo Boite::estimation($mission['mission_id']); ?></strong> électeurs.<br>
					<?php if (is_null($mission['mission_deadline']) || $mission['mission_deadline'] == '0000-00-00') { ?>
					Cette mission n'a pas de date limite connue.
					<?php } else { ?>
					Cette mission doit être terminée pour le <strong><?php echo date('d/m/Y', strtotime($mission['mission_deadline'])); ?></strong>.
					<?php } ?>
				</p>
			</li>
			<?php endforeach; ?>
		</ul>
		<a class="nostyle" href="<?php Core::tpl_go_to('boite', array('action' => 'nouveau')); ?>"><button>Créer une nouvelle mission</button></a>
	</section>
<?php else : ?>
	<section class="icone" id="aucuneMission">
		<h3>Aucune mission lancée actuellement !</h3>
		<a class="nostyle" href="<?php Core::tpl_go_to('boite', array('action' => 'nouveau')); ?>"><button>Créer une mission</button></a>
	</section>
<?php endif; ?>
<?php Core::tpl_footer(); ?>