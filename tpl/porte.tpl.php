<?php
    // On protège l'accès aux administrateurs uniquement
    User::protection(5);
    
    // On défini le type de la mission demandée
    $type = 'porte';
    
    // On récupère la liste des missions
    $missions = Mission::missions($type);
    
    // On charge le template de header
    Core::loadHeader();
?>
	<a href="<?php Core::goPage('porte', array('action' => 'missions')); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Interface militant</button></a>	
	<h2>Porte à porte</h2>
	
    <?php if ($missions) : ?>
		<section id="missions">
			<ul class="liste-missions">
				<?php foreach ($missions as $mission) : $mission = new Mission(md5($mission['mission_id'])); $parcours = $mission->missionStats(); ?>
				<li>
    	    	    <a href="<?php Core::goPage('mission', array('code' => md5($mission->get('mission_id')))); ?>" class="nostyle"><button style="float: right; margin-top: 1.33em;">Ouvrir la mission</button></a>
					<a href="<?php Core::goPage('mission', array('code' => md5($mission->get('mission_id')))); ?>" class="nostyle"><h4><?php echo $mission->get('mission_nom'); ?></h4></a>
					<p>
        <?php if (!$parcours['attente']) : ?>
							Il n'y a plus d'électeurs à visiter.
        <?php else : ?>
							Cette mission comporte encore <strong><?php echo number_format($parcours['attente'], 0, ',', ' '); ?></strong> électeurs à visiter.<br>
        <?php endif; ?>
					</p>
					<p>
        <?php if ($mission->contactsCount(0) && is_null($mission->get('mission_deadline'))) : ?>
							Cette mission n'a pas de deadline connue.
        <?php elseif ($mission->contactsCount(0)) : ?>
							Cette mission doit être terminée pour le <strong><?php echo date('d/m/Y', strtotime($mission->get('mission_deadline'))); ?></strong>.
        <?php endif; ?>
					</p>
				</li>
				<?php endforeach; ?>
			</ul>
			<a class="nostyle" href="<?php Core::goPage('porte', array('action' => 'nouveau')); ?>"><button>Créer une nouvelle mission</button></a>
		</section>
    <?php else : ?>
		<section class="icone" id="aucuneMission">
			<h3>Aucune mission lancée actuellement !</h3>
			<a class="nostyle" href="<?php Core::goPage('porte', array('action' => 'nouveau')); ?>"><button>Créer une mission</button></a>
		</section>
    <?php endif; ?>
	
<?php Core::loadFooter(); ?>