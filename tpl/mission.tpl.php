<?php
	// On protège la page
	User::protection(5);

	// On ouvre la mission
	$data = new Mission($_GET['code']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::goPage('porte', true);
	
	// On récupère les statistiques sur les militants
	$militants = $data->userStats();
	
	// On récupère les statistiques sur le parcours
	$parcours = $data->missionStats();
	
	// On calcule le temps approximatif nécessaire en comptant 3 minutes par électeur
	if ($militants['inscrit']) {
    	$temps = ($parcours['attente'] / $militants['inscrit']) * 3; // 3 minutes par électeur par militant (1,5 minutes en vrai, mais ils sont en binômes)
    	$temps = $temps / 60; // passage en heure
	} else {
    	$temps = false;
	}
	
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
        <h4>Supervision des militants</h4>
        
        <?php if ($militants) : ?>
            <ul class="informations">
                <li class="responsable">
                    <span>Responsable</span>
                    <span><?php echo User::get_login_by_ID($data->get('responsable_id')); ?></span>
                </li>
                <li class="actif<?php echo (!$militants['reporting']) ? '-inconnu' : ''; ?>">
                    <span>Militant le plus actif</span>
                    <?php if ($militants['reporting']) : ?>
                    <span><?php echo User::get_login_by_ID($militants['actif']); ?></span>
                    <?php else : ?>
                    <span>Aucun militant actif pour l'instant</span>
                    <?php endif; ?>
                </li>
                <li class="utilisateurs inscrit">
                    <span>Militants inscrits</span>
                    <span><strong><?php echo ($militants['inscrit']) ? $militants['inscrit'] : 'Aucun'; ?></strong> militant<?php echo ($militants['inscrit'] > 1) ? 's' : ''; ?> inscrit<?php echo ($militants['inscrit'] > 1) ? 's' : ''; ?></span>
                </li>
                <li class="inscrire invitation">
                    <span>Militants invités</span>
                    <span><strong><?php echo ($militants['invitation']) ? $militants['invitation'] : 'Aucun'; ?></strong> militant<?php echo ($militants['invitation'] > 1) ? 's' : ''; ?> invité<?php echo ($militants['invitation'] > 1) ? 's' : ''; ?></span>
                </li>
                <li class="refus">
                    <span>Refus</span>
                    <span><strong><?php echo ($militants['refus']) ? $militants['refus'] : 'Aucun'; ?></strong> militant<?php echo ($militants['refus'] > 1) ? 's' : ''; ?> ayant refusé l'invitation</span>
                </li>
            </ul>
        <?php else : ?>
            <div class="aucunMilitant">
                Aucun militant inscrit ou invité à cette mission
            </div>
        <?php endif; ?>
    </section>

    <section class="contenu demi">
        <?php if ($data->get('mission_statut')) : ?>
            <a href="ajax.php?script=mission-arret&code=<?php echo $data->get('mission_hash'); ?>" class="nostyle"><button class="deleting long" style="margin: .25em auto .15em;">Arrêter la mission</button></a>
        <?php else : ?>
            <a href="ajax.php?script=mission-debut&code=<?php echo $data->get('mission_hash'); ?>" class="nostyle"><button class="long" style="margin: .25em auto .15em;">Publier la mission</button></a>
        <?php endif; ?>
    </section>
</div>

<div class="colonne demi droite">
    <section class="contenu demi">
        <h4>Supervision de la mission</h4>
    
		<?php
			$nombre['attente']     = $parcours['attente'];
			$nombre['absent']      = $parcours['absent'];
			$nombre['ouvert']      = $parcours['ouvert'];
			$nombre['procuration'] = $parcours['procuration'];
			$nombre['contact']     = $parcours['contact'];
			$nombre['npai']        = $parcours['npai'];
			$nombre['total']       = $parcours['total'];
			$nombre['fait']        = $parcours['fait'];
			
			function pourcentage( $actu , $total ) {
				$pourcentage = $actu * 100 / $total;
				$pourcentage = str_replace(',', '.', $pourcentage);
				return $pourcentage;
			}
		?>
		<?php if ($nombre['total']) : ?>
    		<div id="avancementMission"><!--
    		 --><div class="fait" style="width: <?php echo pourcentage($nombre['fait'], $nombre['total']); ?>%;"><span>Portion&nbsp;réalisée&nbsp;de&nbsp;la&nbsp;mission&nbsp;:&nbsp;<?php echo ceil(pourcentage($nombre['fait'], $nombre['total'])); ?>&nbsp;%</span>&nbsp;&nbsp;&nbsp;<?php echo $parcours['proportion']; ?>&nbsp;%</div><!--
    	 --></div>
    	 
    	    <?php if ($data->get('mission_type') == 'porte' && $nombre['fait']) : ?>
    		<h4>Détail des portes frappées</h4>
    		<div id="avancementMission"><!--
    		 --><div class="ouvert" style="width: <?php echo pourcentage($nombre['ouvert'], $nombre['fait']); ?>%;"><span>Portes&nbsp;ouvertes&nbsp;:&nbsp;<?php echo floor(pourcentage($nombre['ouvert'], $nombre['fait'])); ?>&nbsp;%</span></div><!--
    		 --><div class="procuration" style="width: <?php echo pourcentage($nombre['procuration'], $nombre['fait']); ?>%;"><span>Procurations&nbsp;demandées&nbsp;:&nbsp;<?php echo floor(pourcentage($nombre['procuration'], $nombre['fait'])); ?>&nbsp;%</span></div><!--
    		 --><div class="contact" style="width: <?php echo pourcentage($nombre['contact'], $nombre['fait']); ?>%;"><span>Contact&nbsp;souhaité&nbsp;:&nbsp;<?php echo floor(pourcentage($nombre['contact'], $nombre['fait'])); ?>&nbsp;%</span></div><!--
    		 --><div class="absent" style="width: <?php echo pourcentage($nombre['absent'], $nombre['fait']); ?>%;"><span>Contact&nbsp;absent&nbsp;:&nbsp;<?php echo floor(pourcentage($nombre['absent'], $nombre['fait'])); ?>&nbsp;%</span></div><!--
    		 --><div class="npai" style="width: <?php echo pourcentage($nombre['npai'], $nombre['fait']); ?>%;"><span>Adresse&nbsp;erronée&nbsp;:&nbsp;<?php echo floor(pourcentage($nombre['npai'], $nombre['fait'])); ?>&nbsp;%</span></div><!--
    	 --></div>
    	    <?php endif; ?>
    	    
    		<h4>Statistiques</h4>
    		
    		<ul class="statistiquesMission">
    			<li>Mission réalisée à <strong><?php echo ceil(pourcentage($nombre['fait'], $nombre['total'])); ?></strong>&nbsp;%.</li>
    			<li><strong><?php echo number_format($nombre['total'], 0, ',', ' '); ?></strong>&nbsp;<?php echo ($data->get('mission_type') == 'porte') ? 'électeurs' : 'immeubles'; ?> concernés par cette mission.</li>
    			<li>Il reste <strong><?php echo number_format($nombre['attente'], 0, ',', ' '); ?></strong>&nbsp;<?php echo ($data->get('mission_type') == 'porte') ? 'électeurs' : 'immeubles'; ?> à visiter.</li>
                <?php if ($temps) : ?>
                <li>Mission estimée à <strong><?php echo round($temps, 0); ?> heures</strong>.</li>
                <?php endif; ?>
    		</ul>
	    <?php else : ?>
    	    <p>Cette mission n'a pas encore de parcours.</p>
	    <?php endif; ?>
    </section>
</div>