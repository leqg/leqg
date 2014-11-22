<?php
    // On charge les informations sur la mission
    $mission = Boite::informations(md5($_GET['mission']))[0];
    
    // On charge le template
    Core::tpl_header();
?>

<h2 id="titre-mission" data-mission="<?php echo md5($mission['mission_id']); ?>">Boîtage &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

<!-- Blocs de mission vide -->
<div class="colonne demi gauche">
    <section id="boitage-vide" class="contenu demi icone rue <?php if (Boite::nombreImmeubles($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
    	<h3>Aucun immeuble à visiter actuellement.</h3>
    </section>
    
    <section id="boitage-afaire" class="contenu demi <?php if (!Boite::nombreImmeubles($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
    	<h4>Rues au sein de cette mission</h4>
    	
    	<?php $rues = Boite::liste($mission['mission_id']); ?>
    	<ul class="form-liste" id="listeDesRues">
    		<?php
    			// On met en place un tri des rues à partir de leur nom
    			$indexRues = array();
    			foreach ($rues as $rue => $immeubles) { $indexRues[$rue] = Carto::afficherRue($rue, true); }
    			natsort($indexRues);
    			
    			foreach ($indexRues as $rue => $nom) {
    		?>
    		<li id="immeubles-rue-<?php $rue; ?>">
    			<button class="voirRue gris" data-rue="<?php echo $rue; ?>" data-nom="<?php echo $nom; ?>">Consulter</button>
    			<span><?php echo $nom; ?></span>
    			<span><?php Carto::afficherVille(Carto::villeParRue($rue)); ?></span>
    		</li>
    		<?php } ?>
    	</ul>
    </section>
</div>

<div class="colonne demi droite">
    <?php if (Boite::nombreImmeubles($mission['mission_id'], 1)) { ?>
    	<section id="boitage-statistiques" class="contenu demi">
    		<h4>Avancement de la mission</h4>
    		<?php
    			// On réalise les calculs en nombre d'électeurs
    			$electeursFait = Boite::estimation($mission['mission_id'], 1);
    			$electeursRestant = Boite::estimation($mission['mission_id'], 0);
    			$electeursTotal = $electeursFait + $electeursRestant;
    			
    			$nombreTotal = Boite::nombreImmeubles($mission['mission_id'], -1);
    			$nombreFait = Boite::nombreImmeubles($mission['mission_id'], 1);
    			$nombreRestant = $electeursTotal - $electeursFait;
    		
    			// On fabrique les pourcentages
    			$fait = ($nombreFait * 100) / $electeursTotal;
    			$afaire = 100 - $fait;
    		?>
    		<div id="avancementMission"><div style="width: <?php echo ceil($fait); ?>%;"><?php if ($fait >= 10) { echo ceil($fait); ?>&nbsp;%<?php } ?></div></div>
    	</section>
    <?php } else { ?>
    	<section id="boitage-statistiques" class="contenu demi icone fusee">
    		<h3>La mission n'a pas été commencée.</h3>
    		<?php if (Boite::nombreImmeubles($mission['mission_id'], 0)) { ?>
    			<h5>Il existe <span><?php echo Boite::estimation($mission['mission_id']); ?></span> électeurs à boiter.</h5>
    		<?php } ?>
    	</section>
    <?php } ?>
    
    <section id="listeImmeublesParRue" class="contenu demi invisible">
    	<h4 class="nomRue">Immeubles restant à boiter <strong><span></span></strong></h4>
    	
    	<ul class="form-liste"></ul>
    </section>
</div>