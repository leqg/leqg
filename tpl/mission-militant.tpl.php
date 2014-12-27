<?php
	// On protège la page
	User::protection(5);

	// On ouvre la mission
	$data = new Mission($_GET['code']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::tpl_go_to('porte', true);
	
	// On récupère la liste des militants par statut d'invitation
	$militants[1] = $data->liste_inscrits(1);
	$militants[0] = $data->liste_inscrits(0);
	$militants[-1] = $data->liste_inscrits(-1);

    // On charge le header
	Core::tpl_header();
?>

<h2 id="titre-mission" class="titre" data-mission="<?php echo $data->get('mission_hash'); ?>">Porte-à-porte &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'), 'admin' => 'militant')); ?>">Militants</a>
    <a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'), 'admin' => 'parcours')); ?>">Parcours</a>
    <a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'), 'admin' => 'options')); ?>">Administration</a>
</nav>

<div class="colonne demi gauche">
    <section class="contenu demi">
        <h4>Militants inscrits à la mission</h4>
        
        <ul class="listeContacts">
            <?php if ($militants[1]) : foreach ($militants[1] as $militant) : ?>
			<li class="contact user homme"><?php echo User::get_login_by_ID($militant['user_id']); ?></li>
			<?php endforeach; else : ?>
			<li class="contact user homme">Aucun militant inscrit.</li>
            <?php endif; ?>
        </ul>
    </section>
    
    <section class="contenu demi">
        <h4>Militants ayant refusé l'invitation</h4>
        
        <ul class="listeContacts">
            <?php if ($militants[-1]) : foreach ($militants[-1] as $militant) : ?>
			<li class="contact user homme"><?php echo User::get_login_by_ID($militant['user_id']); ?></li>
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
            <a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'), 'admin' => 'invitations')); ?>" class="nostyle"><li class="contact ajout">Inviter de nouveaux militants à la mission</li></a>
            <?php if ($militants[0]) : foreach ($militants[0] as $militant) : ?>
			<li class="contact user homme"><?php echo User::get_login_by_ID($militant['user_id']); ?></li>
			<?php endforeach; else : ?>
			<li class="contact user homme">Aucun militant invité.</li>
            <?php endif; ?>
        </ul>
    </section>
</div>