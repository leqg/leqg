<?php
	// On commence par vérifier qu'il existe bien une mission, et si oui on l'affiche
	if (isset($_GET['mission']) && $porte->verification($_GET['mission'])) {
		$mission = $porte->informations($_GET['mission']);
		$core->tpl_header();
	} else {
		$core->tpl_go_to('porte', true);
	}
?>

<h2 id="titre-mission" data-mission="<?php echo md5($mission['mission_id']); ?>">Porte-à-porte &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

<!-- Blocs de mission vide -->
<section id="porte-vide" class="icone rue demi gauche <?php if ($porte->nombreVisites($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
	<h3>Aucun immeuble à visiter actuellement.</h3>
	<div class="coteAcote">
		<button class="ajouterRue">Ajouter une rue</button>
		<button class="ajouterBureau">Ajouter un bureau</button>
	</div>
</section>

<section id="porte-afaire" class="demi gauche <?php if (!$porte->nombreVisites($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
	<div class="coteAcote haut">
		<button class="ajouterRue">Ajouter une rue</button>
		<button class="ajouterBureau">Ajouter un bureau</button>
	</div>

	<h4>Rues au sein de cette mission</h4>
	
	<?php $rues = $porte->liste($mission['mission_id']);?>
	<ul class="form-liste" id="listeDesRues">
		<?php
			// On met en place un tri des rues à partir de leur nom
			$indexRues = array();
			foreach ($rues as $rue => $immeubles) { $indexRues[$rue] = $carto->afficherRue($rue, true); }
			natsort($indexRues);
			
			foreach ($indexRues as $rue => $nom) {
		?>
		<li id="immeubles-rue-<?php $rue; ?>">
			<button class="voirRue gris" data-rue="<?php echo $rue; ?>" data-nom="<?php echo $nom; ?>">Consulter</button>
			<span><?php echo $nom; ?></span>
			<span><?php $carto->afficherVille($carto->villeParRue($rue)); ?></span>
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

<?php if ($porte->nombreVisites($mission['mission_id'], 1)) { ?>
	<section id="porte-statistiques" class="demi droite">
		<h4>Avancement de la mission</h4>
		<?php
			// On réalise les calculs en nombre d'électeurs
			$electeursFait = $porte->estimation($mission['mission_id'], 1);
			$electeursRestant = $porte->estimation($mission['mission_id'], 0);
			$electeursTotal = $electeursFait + $electeursRestant;
			
			$nombreTotal = $porte->nombreVisites($mission['mission_id'], -1);
			$nombreFait = $porte->nombreVisites($mission['mission_id'], 1);
			$nombreRestant = $nombreTotal - $nombreFait;
		
			// On fabrique les pourcentages
			$fait = ($nombreFait * 100) / $nombreTotal;
			$afaire = ($nombreRestant * 100) / $nombreTotal;
		?>
		<div id="avancementMission"><div style="width: <?php echo ceil($fait); ?>%;"><?php if ($fait >= 10) { echo ceil($fait); ?>&nbsp;%<?php } ?></div></div>
		
		<h4>Statistiques</h4>
		<ul class="statistiquesMission">
			<li>Mission réalisée à <strong><?php echo ceil($fait); ?></strong>&nbsp;%</li>
			<li><strong><?php echo number_format($electeursTotal, 0, ',', ' '); ?></strong>&nbsp;électeurs concernés par cette mission</li>
			<li>Il reste <strong><?php echo number_format($electeursRestant, 0, ',', ' '); ?></strong>&nbsp;électeurs à visiter.</li>
		</ul>
		
		<a href="<?php echo Core::tpl_go_to('porte', array('reporting' => md5($mission['mission_id']))); ?>" class="nostyle"><button class="long" style="margin: 2.5em auto .33em;">Effectuer un reporting</button></a>
	</section>
<?php } else { ?>
	<section id="porte-statistiques" class="icone fusee demi droite">
		<h3>La mission n'a pas été commencée.</h3>
		<?php if ($porte->nombreVisites($mission['mission_id'], 0)) { ?>
			<h5>Il existe <span><?php echo number_format($porte->estimation($mission['mission_id']), 0, ',', ' '); ?></span> électeurs à visiter.</h5>
		<?php } ?>
		
		<a href="<?php echo Core::tpl_go_to('porte', array('reporting' => md5($mission['mission_id']))); ?>" class="nostyle"><button class="long" style="margin: 2.5em auto .33em;">Effectuer un reporting</button></a>
	</section>
<?php } ?>

<section id="ajoutRue" class="demi droite invisible">
	<ul class="formulaire">
		<li>
			<label>Recherchez une rue</label>
			<span class="form-icon street"><input type="text" name="rechercheRue" id="rechercheRue" placeholder="rue du Marché"></span>
		</li>
	</ul>
	<ul class="form-liste invisible" id="listeRues"></ul>
</section>

<section id="ajoutBureau" class="demi droite invisible">
	<ul class="formulaire">
		<li>
			<label>Recherchez un bureau de vote</label>
			<span class="form-icon street"><input type="text" name="rechercheBureau" id="rechercheBureau" placeholder="103 ou École des Champs"></span>
		</li>
	</ul>
	<ul class="form-liste invisible" id="listeBureaux"></ul>
</section>

<section id="choixImmeuble" class="demi droite invisible">
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

<section id="listeImmeublesParRue" class="demi droite invisible">
	<h4 class="nomRue">Immeubles restant à visiter <strong><span></span></strong></h4>
	
	<ul class="form-liste"></ul>
</section>
