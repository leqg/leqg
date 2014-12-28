<?php
	// On protège la page
	User::protection(5);

	// On ouvre la mission
	$data = new Mission($_GET['code']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::tpl_go_to('porte', true);
	
	// On récupère la liste des rues de la mission
    $rues = $data->rues();
	
	// typologie
	$typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
	Core::tpl_header();
?>
<a href="<?php Core::tpl_go_to($typologie); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Revenir à la liste</button></a>	
<h2 id="titre-mission" class="titre" data-mission="<?php echo $data->get('mission_hash'); ?>">Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'), 'admin' => 'militant')); ?>">Militants</a>
    <a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'), 'admin' => 'parcours')); ?>">Parcours</a>
    <?php if ($data->get('mission_type') == 'porte') { ?><a href="<?php Core::tpl_go_to('mission', array('code' => $data->get('mission_hash'), 'admin' => 'retours')); ?>">Demandes</a><?php } ?>
</nav>

<div class="colonne demi gauche">
    <section class="contenu demi">
        <h4>Rues concernées par cette mission</h4>
        
        <?php if ($rues) : ?>
        <ul class="form-liste" id="listeDesRues">
            <?php foreach ($rues as $rue) : $stats = $data->statistique_rue($rue['rue_id']); ?>
        	<li>
        		<a href="<?php Core::tpl_go_to('reporting', array('mission' => $data->get('mission_hash'), 'rue' => $rue['rue_id'])); ?>" class="nostyle"><button class="voirRue gris">Voir la fiche</button></a>
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

<div class="colonne demi droite">
	<section id="ajoutRue" class="contenu demi">
		<ul class="formulaire">
			<li>
				<label>Ajout d'une rue</label>
				<span class="form-icon street"><input type="text" name="rechercheRue" id="rechercheRue" placeholder="rue du Marché"></span>
			</li>
		</ul>
		<ul class="form-liste invisible" id="listeRues"></ul>
	</section>
	
	<section id="ajoutBureau" class="contenu demi">
		<ul class="formulaire">
			<li>
				<label>Ajout d'un bureau de vote</label>
				<span class="form-icon street"><input type="text" name="rechercheBureau" id="rechercheBureau" placeholder="103 ou École des Champs"></span>
			</li>
		</ul>
		<ul class="form-liste invisible" id="listeBureaux"></ul>
	</section>
</div>