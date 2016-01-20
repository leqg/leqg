<?php
	// On protège la page
	User::protection(5);

	// On ouvre la mission
	$data = new Mission($_GET['code']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::goPage('porte', true);
	
	// On récupère la liste des militants par statut d'invitation
	$militants[1] = $data->missionMembers(1);
	$militants[0] = $data->missionMembers(0);
	$militants[-1] = $data->missionMembers(-1);
	
	// typologie
	$typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
	Core::loadHeader();
?>
<a href="<?php Core::goPage($typologie); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Revenir à la liste</button></a>	
<h2 id="titre-mission" class="titre" data-mission="<?php echo $data->get('mission_hash'); ?>">Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

<nav class="onglets">
    <a href="<?php Core::goPage('mission', array('code' => $data->get('mission_hash'))); ?>">Supervision</a>
    <a href="<?php Core::goPage('mission', array('code' => $data->get('mission_hash'), 'admin' => 'militant')); ?>">Militants</a>
    <a href="<?php Core::goPage('mission', array('code' => $data->get('mission_hash'), 'admin' => 'parcours')); ?>">Parcours</a>
    <?php if ($data->get('mission_type') == 'porte') { ?><a href="<?php Core::goPage('mission', array('code' => $data->get('mission_hash'), 'admin' => 'retours')); ?>">Demandes</a><?php } ?>
</nav>

<div class="colonne demi gauche">
    <section class="contenu demi">
        <h4>Militants inscrits à la mission</h4>
        
        <ul class="listeContacts">
            <?php if ($militants[1]) : foreach ($militants[1] as $militant) : ?>
			<li class="contact user homme"><?php echo User::getLoginByID($militant['user_id']); ?></li>
			<?php endforeach; else : ?>
			<li class="contact user homme">Aucun militant inscrit.</li>
            <?php endif; ?>
        </ul>
    </section>
    
    <section class="contenu demi">
        <h4>Militants ayant refusé l'invitation</h4>
        
        <ul class="listeContacts">
            <?php if ($militants[-1]) : foreach ($militants[-1] as $militant) : ?>
			<li class="contact user homme"><?php echo User::getLoginByID($militant['user_id']); ?></li>
			<?php endforeach; else : ?>
			<li class="contact user homme">Aucun militant ayant refusé l'invitation.</li>
            <?php endif; ?>
        </ul>
    </section>
</div>

<div class="colonne demi droite">
    <section class="contenu demi">
        <h4>Militants invités</h4>
        
        <ul class="listeContacts">
            <a href="<?php Core::goPage('mission', array('code' => $data->get('mission_hash'), 'admin' => 'invitations')); ?>" class="nostyle"><li class="contact ajout">Inviter de nouveaux militants à la mission</li></a>
            <?php if ($militants[0]) : foreach ($militants[0] as $militant) : ?>
			<li class="contact user homme"><?php echo User::getLoginByID($militant['user_id']); ?></li>
			<?php endforeach; else : ?>
			<li class="contact user homme">Aucun militant invité.</li>
            <?php endif; ?>
        </ul>
    </section>
</div>