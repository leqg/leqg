<?php
	$mission = Porte::informations(md5($_GET['mission']))[0];
	
	// On ouvre la mission
	$data = new Mission(md5($_GET['mission']));
	
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
				$nombre['attente']     = $data->nombre_contacts(0);
				$nombre['absent']      = $data->nombre_contacts(1);
				$nombre['ouvert']      = $data->nombre_contacts(2);
				$nombre['procuration'] = $data->nombre_contacts(3);
				$nombre['contact']     = $data->nombre_contacts(4);
				$nombre['npai']        = $data->nombre_contacts(-1);
				$nombre['total']       = array_sum($nombre);
				$nombre['fait']        = $nombre['total'] - $nombre['attente'];
				
				function pourcentage( $actu , $total ) {
					$pourcentage = $actu * 100 / $total;
					$pourcentage = str_replace(',', '.', $pourcentage);
					return $pourcentage;
				}
			?>
			
			<h4>Avancement de la mission</h4>
			<div id="avancementMission"><!--
			 --><div class="fait" style="width: <?php echo pourcentage($nombre['fait'], $nombre['total']); ?>%;"><span>Portion&nbsp;réalisée&nbsp;de&nbsp;la&nbsp;mission&nbsp;(<?php echo ceil(pourcentage($nombre['fait'], $nombre['total'])); ?>&nbsp;%)</span></div><!--
		 --></div>
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