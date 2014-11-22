<?php
    // On protège le répertoire
    User::protection(5);
    
    // On charge les informations sur la mission
    $mission = Boite::informations($_GET['mission'])[0];
    
    // On charge le template
    Core::tpl_header();
?>

<h2 id="titre-mission" data-mission="<?php echo md5($mission['mission_id']); ?>">Boîtage &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

<!-- Blocs de mission vide -->
<div class="colonne demi gauche">
    <section id="boitage-vide" class="contenu demi icone rue <?php if (Boite::nombreImmeubles($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
    	<h3>Aucun immeuble à visiter actuellement.</h3>
    	<div class="coteAcote">
    		<button class="ajouterRue">Ajouter une rue</button>
    		<button class="ajouterBureau">Ajouter un bureau</button>
    	</div>
    </section>
    
    <section id="boitage-afaire" class="contenu demi <?php if (!Boite::nombreImmeubles($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
    	<div class="coteAcote haut">
    		<button class="ajouterRue">Ajouter une rue</button>
    		<button class="ajouterBureau">Ajouter un bureau</button>
    	</div>
    
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
    		
    		<h4>Statistiques</h4>
    		<ul class="statistiquesMission">
    			<li>Mission réalisée à <strong><?php echo ceil($fait); ?></strong>&nbsp;%</li>
    			<li><strong><?php echo number_format($electeursTotal, 0, ',', ' '); ?></strong>&nbsp;électeurs concernés par cette mission</li>
    			<li>Il reste <strong><?php echo number_format($electeursRestant, 0, ',', ' '); ?></strong>&nbsp;électeurs à boiter.</li>
    		</ul>
    	</section>
    <?php } else { ?>
    	<section id="boitage-statistiques" class="contenu demi icone fusee">
    		<h3>La mission n'a pas été commencée.</h3>
    		<?php if (Boite::nombreImmeubles($mission['mission_id'], 0)) { ?>
    			<h5>Il existe <span><?php echo Boite::estimation($mission['mission_id']); ?></span> électeurs à boiter.</h5>
    		<?php } ?>
    	</section>
    <?php } ?>
	
	<section class="contenu demi">
		<h4>Militants inscrits à cette mission</h4>
		
		<ul class="listeContacts">
			<?php $comptes = Porte::inscriptions($mission['mission_id']); if (count($comptes)) : foreach($comptes as $compte) : ?>
			<li class="contact homme"><?php echo User::get_login_by_ID($compte['user_id']); ?></li>
			<?php endforeach; else : ?>
			<li class="contact homme">Aucune inscription actuellement.</li>
			<?php endif; ?>
		</ul>
	</section>
    
    <section id="ajoutRue" class="contenu demi invisible">
    	<ul class="formulaire">
    		<li>
    			<label>Recherchez une rue</label>
    			<span class="form-icon street"><input type="text" name="rechercheRue" id="rechercheRue" placeholder="rue du Marché"></span>
    		</li>
    	</ul>
    	<ul class="form-liste invisible" id="listeRues"></ul>
    </section>
    
    <section id="ajoutBureau" class="contenu demi invisible">
    	<ul class="formulaire">
    		<li>
    			<label>Recherchez un bureau de vote</label>
    			<span class="form-icon street"><input type="text" name="rechercheBureau" id="rechercheBureau" placeholder="103 ou École des Champs"></span>
    		</li>
    	</ul>
    	<ul class="form-liste invisible" id="listeBureaux"></ul>
    </section>
    
    <section id="choixImmeuble" class="contenu demi invisible">
    	<ul class="formulaire">
    		<li>
    			<label>Sélectionnez des immeubles</label>
    			<span class="form-icon street"><input type="text" name="rueSelectionImmeuble" id="rueSelectionImmeuble" value=""></span>
    		</li>
    	</ul>
    	
    	<ul class="form-liste">
    		<li>
    			<button class="ajouterLaRue" id="rueEntiere" data-rue="" data-mission="<?php echo $_GET['mission']; ?>">Ajouter</button>
    			<span class="immeuble-numero">Ajouter tous les immeubles de la rue</span>
    			<span class="nombre-electeurs"></span>
    		</li>
    	</ul>
    </section>
    
    <section id="listeImmeublesParRue" class="contenu demi invisible">
    	<h4 class="nomRue">Immeubles restant à boiter <strong><span></span></strong></h4>
    	
    	<ul class="form-liste"></ul>
    </section>
</div>