<?php
	User::protection(5);
	// On commence par vérifier qu'il existe bien une mission, et si oui on l'affiche
	if (isset($_GET['mission']) && Porte::verification($_GET['mission'])) {
		$mission = Porte::informations($_GET['mission'])[0];
		Core::tpl_header();
	} else {
		Core::tpl_go_to('porte', true);
	}
?>

<h2 id="titre-mission" data-mission="<?php echo md5($mission['mission_id']); ?>">Porte-à-porte &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

<!-- Blocs de mission vide -->
<div class="colonne demi gauche">
	<section id="porte-vide" class="icone rue contenu demi <?php if (Porte::nombreVisites($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
		<h3>Aucun immeuble à visiter actuellement.</h3>
		<div class="coteAcote">
			<button class="ajouterRue">Ajouter rue</button>
			<button class="ajouterBureau">Ajouter bureau</button>
		</div>
	</section>
	
	<section id="porte-afaire" class="contenu demi <?php if (!Porte::nombreVisites($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
		<div class="coteAcote haut">
			<button class="ajouterRue">Ajouter rue</button>
			<button class="ajouterBureau">Ajouter bureau</button>
		</div>
	
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
				<button class="ajouterRue">Ajouter rue</button>
				<button class="ajouterBureau">Ajouter bureau</button>
			</div>
		<?php } ?>
	</section>
	
	<section class="contenu demi">
    	<button class="deleting long supprimerMission" style="margin: .25em auto .15em;">Suppression de la mission</button>
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
			
			<h4>Statistiques</h4>
			<ul class="statistiquesMission">
				<li>Mission réalisée à <strong><?php echo ceil($fait); ?></strong>&nbsp;%</li>
				<li><strong><?php echo number_format($electeursTotal, 0, ',', ' '); ?></strong>&nbsp;électeurs concernés par cette mission</li>
				<li>Il reste <strong><?php echo number_format($electeursRestant, 0, ',', ' '); ?></strong>&nbsp;électeurs à visiter.</li>
			</ul>
			
			<a href="<?php echo Core::tpl_go_to('porte', array('reporting' => md5($mission['mission_id']))); ?>" class="nostyle"><button class="long" style="margin: 2.5em auto .33em;">Reporting de la mission</button></a>
		</section>
	<?php } else { ?>
		<section id="porte-statistiques" class="icone fusee contenu demi">
			<h3>La mission n'a pas été commencée.</h3>
			<?php if (Porte::nombreVisites($mission['mission_id'], 0)) { ?>
				<h5>Il existe <span><?php echo number_format(Porte::estimation($mission['mission_id']), 0, ',', ' '); ?></span> électeurs à visiter.</h5>
			<?php } ?>
			
			<a href="<?php echo Core::tpl_go_to('porte', array('reporting' => md5($mission['mission_id']))); ?>" class="nostyle"><button class="long" style="margin: 2.5em auto .33em;">Reporting de la mission</button></a>
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
		<h4 class="nomRue">Immeubles restant à visiter <strong><span></span></strong></h4>
		
		<ul class="form-liste"></ul>
	</section>
</div>