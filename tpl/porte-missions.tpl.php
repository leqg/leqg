<?php
    // On met en place la protection
    User::protection(1);

    // On charge la liste des missions où la personne est invitée
    $invitations = Mission::invitations('porte', User::ID());
    
    // On charge la liste des missions ouvertes où la personne est inscrite
    $missions_ouvertes = Mission::missions_ouvertes('porte', User::ID());
    
    // On charge le header
    Core::loadHeader();
?>
    <?php if (User::auth_level() >= 5) : ?><a href="<?php Core::goPage('porte'); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Administration</button></a><?php 
    endif; ?>
	<h2 class="titre" data-user="<?php echo User::ID(); ?>">Porte à porte</h2>
	
    <?php if ($invitations) : ?>
	<section id="missions">
		<h3 class="titrebloc">Invitations en cours</h3>
		
		<ul class="liste-missions">
        <?php foreach ($invitations as $invitation) : $mission = new Mission(md5($invitation)); ?>
    		<li>
    		    <a href="ajax.php?script=mission-refuser&code=<?php echo $mission->get('mission_hash'); ?>&user=<?php echo md5(User::ID()); ?>" class="nostyle"><button class="deleting" style="float: right; margin-top: 1.33em;">Refuser</button></a>
    		    <a href="ajax.php?script=mission-accepter&code=<?php echo $mission->get('mission_hash'); ?>&user=<?php echo md5(User::ID()); ?>" class="nostyle"><button class="vert" style="float: right; margin-top: 1.33em; margin-right: 1em;">Accepter</button></a>
    		    <h4><?php echo $mission->get('mission_nom'); ?></h4>
    		    <p>Vous êtes invité à participer à cette mission par <em><?php echo User::get_login_by_ID($mission->get('responsable_id')); ?></em>.</p>
    		</li>
        <?php endforeach; ?>
		</ul>
	</section>
    <?php endif; ?>
		
    <?php if ($missions_ouvertes) : ?>
	<section id="missions">
		<h3 class="titrebloc">Missions ouvertes auxquelles vous participez</h3>
		
		<ul class="liste-missions">
        <?php foreach ($missions_ouvertes as $mission_ouverte) : $mission = new Mission(md5($mission_ouverte)); $deadline = DateTime::createFromFormat('Y-m-d', $mission->get('mission_deadline')); ?>
    		<li>
    		    <a href="<?php Core::goPage('reporting', array('mission' => $mission->get('mission_hash'))); ?>" class="nostyle"><button style="float: right; margin-top: 1.33em;">Ouvrir la mission</button></a>
    		    <a href="<?php Core::goPage('reporting', array('mission' => $mission->get('mission_hash'))); ?>" class="nostyle"><h4><?php echo $mission->get('mission_nom'); ?></h4></a>
            <?php if ($mission->get('mission_deadline')) : ?>
    		    <p>Cette mission de porte-à-porte doit être terminée pour le <strong><?php echo $deadline->format('d/m/Y'); ?></strong>.</p>
            <?php else : ?>
    		    <p>Cette mission n'a pas de date de fin connue.</p>
            <?php endif; ?>
    		</li>
        <?php endforeach; ?>
		</ul>
	</section>
    <?php endif; ?>
			
    <?php if (!$invitations && !$missions_ouvertes) : ?>
	<section class="icone" id="aucuneMission">
		<h3>Aucune mission lancée actuellement !</h3>
	</section>
    <?php endif; ?>
	
<?php Core::loadFooter(); ?>