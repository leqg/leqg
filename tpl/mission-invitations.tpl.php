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
	$comptes = $data->missionMembers();
	
	// On récupère la liste des membres inscrits sur le site, à l'exception de ceux déjà ajoutés
	$users = User::sauf($comptes);
	
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

<section class="contenu">
    <h4>Sélectionnez les membres à inviter</h4>
    
    <ul class="listeContacts">
        <?php if ($users) : foreach ($users as $militant) : ?><!--
	 --><a href="ajax.php?script=mission-invitation&code=<?php echo $data->get('mission_hash'); ?>&user=<?php echo $militant['id']; ?>" class="nostyle"><!--
    	 --><li class="demi contact user homme cursor" data-user="<?php echo $militant['id']; ?>"><?php echo User::getLoginByID($militant['id']); ?></li><!--
     --></a><!--
	 --><?php endforeach; else : ?>
		<li class="contact user homme">Aucun militant n'est disponible pour une invitation.</li>
        <?php endif; ?>
    </ul>
    
    <a href="<?php Core::goPage('mission', array('code' => $data->get('mission_hash'), 'admin' => 'militant')); ?>" class="nostyle"><button class="gris">Revenir à la liste des membres</button></a>
</section>