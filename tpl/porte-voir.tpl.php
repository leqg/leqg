<?php
	$mission = Porte::informations(md5($_GET['mission']))[0];
	Core::tpl_header();
?>

<h2 id="titre-mission" data-mission="<?php echo md5($mission['mission_id']); ?>">Porte-à-porte &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

<!-- Blocs de mission vide -->
<div class="colonne demi gauche">
	<section id="porte-vide" class="icone rue contenu demi <?php if (Porte::nombreVisites($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
		<h3>Aucun immeuble à visiter actuellement.</h3>
	</section>
	
	<section id="porte-afaire" class="contenu demi <?php if (!Porte::nombreVisites($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
		<h4>Rues au sein de cette mission</h4>
		
		<?php $rues = Porte::liste($mission['mission_id']);?>
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
	
		<?php if (count($indexRues) >= 20) { ?>
			<div class="coteAcote">
				<button class="ajouterRue">Ajouter une rue</button>
				<button class="ajouterBureau">Ajouter un bureau</button>
			</div>
		<?php } ?>
	</section>
</div>

<div class="colonne demi droite">
	<?php if (Porte::nombreVisites($mission['mission_id'], 1)) { ?>
		<section id="porte-statistiques" class="contenu demi">
			<h4>Avancement de la mission</h4>
			<?php
				// On réalise les calculs en nombre d'électeurs
				$electeursFait = Porte::estimation($mission['mission_id'], 1);
				$electeursRestant = Porte::estimation($mission['mission_id'], 0);
				$electeursTotal = $electeursFait + $electeursRestant;
				
				$nombreTotal = Porte::nombreVisites($mission['mission_id'], -1);
				$nombreFait = Porte::nombreVisites($mission['mission_id'], 1);
				$nombreRestant = $nombreTotal - $nombreFait;
			
				// On fabrique les pourcentages
				$fait = ($nombreFait * 100) / $electeursTotal;
				$afaire = 100 - $fait;
			?>
			<div id="avancementMission"><div style="width: <?php echo ceil($fait); ?>%;"><?php if ($fait >= 10) { echo ceil($fait); ?>&nbsp;%<?php } ?></div></div>
		</section>
	<?php } else { ?>
		<section id="porte-statistiques" class="icone fusee contenu demi">
			<h3>La mission n'a pas été commencée.</h3>
			<?php if (Porte::nombreVisites($mission['mission_id'], 0)) { ?>
				<h5>Il existe <span><?php echo number_format(Porte::estimation($mission['mission_id']), 0, ',', ' '); ?></span> électeurs à visiter.</h5>
			<?php } ?>
		</section>
	<?php } ?>
	
	<section id="listeImmeublesParRue" class="contenu demi invisible">
		<h4 class="nomRue">Immeubles restant à visiter <strong><span></span></strong></h4>
		
		<ul class="form-liste"></ul>
	</section>
</div>