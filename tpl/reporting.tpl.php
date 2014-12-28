<?php
	// On ouvre la mission
	$data = new Mission($_GET['mission']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::tpl_go_to('porte', true);
	
	// On récupère les statistiques sur les militants
	$militants = $data->statistiques_militant();
	
	// On récupère la liste des rues de la mission
    $rues = $data->rues();
	
	// typologie
	$typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
	Core::tpl_header();
?>
<a href="<?php Core::tpl_go_to($typologie, array('action' => 'missions')); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Retour aux missions</button></a>	
<h2 id="titre-mission" class="titre" data-mission="<?php echo $data->get('mission_hash'); ?>">Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

<div class="colonne demi gauche">
    <section class="contenu demi">
        <h4>Informations génériques</h4>
        
        <?php if ($militants) : ?>
            <ul class="informations">
                <li class="responsable">
                    <span>Votre responsable</span>
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
            </ul>
        <?php else : ?>
            <div class="aucunMilitant">
                Aucun militant inscrit ou invité à cette mission
            </div>
        <?php endif; ?>
    </section>

    <section class="contenu demi">
        <a href="ajax.php?script=mission-quitter&code=<?php echo $data->get('mission_hash'); ?>" class="nostyle"><button class="deleting long" style="margin: .25em auto .15em;">Retirer son inscription</button></a>
    </section>
</div>

<div class="colonne demi droite">
    <section class="contenu demi">
        <h4>Rues à parcourir</h4>
        
        <?php if ($rues) : ?>
        <ul class="form-liste" id="listeDesRues">
            <?php foreach ($rues as $rue) : $stats = $data->statistique_rue($rue['rue_id']); ?>
        	<li>
        		<?php if ($stats['proportion'] < 100) { ?><a href="<?php Core::tpl_go_to('reporting', array('mission' => $data->get('mission_hash'), 'rue' => $rue['rue_id'])); ?>" class="nostyle"><button class="voirRue">Fiche</button></a><?php } ?>
        		<span><?php echo $rue['rue_nom']; ?></span>
        		<?php if ($stats && $stats['proportion']) : ?>
            		<span>Réalisée à <?php echo $stats['proportion']; ?>&nbsp;%</span>
        		<?php elseif ($stats) : ?>
            		<span>Rue non débutée.</span>
        		<?php else : ?>
            		<span>Statistiques indisponibles.</span>
        		<?php endif; ?>
        	</li>
        	<?php endforeach; ?>
        </ul>
        <?php else : ?>
        <p>Aucune rue n'a été sélectionnée pour l'instant.</p>
        <?php endif; ?>
    </section>
</div>