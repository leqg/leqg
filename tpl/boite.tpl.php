<?php
	// On protège l'accès aux administrateurs uniquement
	User::protection(5);
	
	// On défini le type de la mission demandée
	$type = 'boitage';
	
	// On récupère la liste des missions
	$missions = Mission::missions($type);
	
	// On charge le template de header
	Core::tpl_header();
?>
		
	<h2>Campagnes de boîtage</h2>
	
	<?php if ($missions) : ?>
		<section id="missions">
			<ul class="liste-missions">
				<?php foreach ($missions as $mission) : $mission = new Mission(md5($mission['mission_id'])); ?>
				<li>
					<a href="<?php Core::tpl_go_to('boite', array('mission' => md5($mission->get('mission_id')))); ?>" class="nostyle"><h4><?php echo $mission->get('mission_nom'); ?></h4></a>
					<p>
						<?php if (!$mission->nombre_immeubles(0)) : ?>
							Cette mission de boîtage est aujourd'hui terminée.<br>
							Il n'y a plus d'immeuble à visiter.
						<?php else : ?>
							Cette mission comporte encore <strong><?php echo $mission->nombre_immeubles(0); ?></strong> immeubles à boîter.<br>
						<?php endif; ?>
					</p>
					<p>
						<?php if ($mission->nombre_immeubles(0) && is_null($mission->get('mission_deadline'))) : ?>
							Cette mission n'a pas de deadline connue.
						<?php elseif ($mission->nombre_immeubles(0)) : ?>
							Cette mission doit être terminée pour le <strong><?php echo date('d/m/Y', strtotime($mission->get('mission_deadline'))); ?></strong>.
						<?php endif; ?>
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