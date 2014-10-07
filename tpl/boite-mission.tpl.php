<?php
	// On commence par vérifier qu'il existe bien une mission, et si oui on l'affiche
	if (isset($_GET['mission']) && $boitage->verification($_GET['mission'])) {
		$mission = $boitage->informations($_GET['mission']);
		$core->tpl_header();
	} else {
		$core->tpl_go_to('boite', true);
	}
?>

<h2 data-mission="<?php echo $mission['mission_id']; ?>">Boîtage &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

<!-- Blocs de mission vide -->
<section id="boitage-vide" class="icone rue demi gauche <?php if ($boitage->nombreImmeubles($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
	<h3>Aucun immeuble à visiter actuellement.</h3>
	<button class="ajouterRue">Ajouter une rue</button>
	<button class="ajouterBureau">Ajouter un bureau</button>
</section>

<section id="boitage-afaire" class="demi gauche <?php if (!$boitage->nombreImmeubles($mission['mission_id'], 0)) { echo 'invisible'; } ?>">
	<h4>Rues au sein de cette mission</h4>
	
	<?php $rues = $boitage->liste($mission['mission_id']); ?>
	<ul class="form-liste" id="listeDesRues">
		<?php
			foreach ($rues as $rue => $immeubles) {
				$r = $carto->afficherRue($rue, true);
				foreach ($immeubles as $key => $immeuble) {
					$i = $carto->immeuble($immeuble);
					$immeubles[$key] = $i['numero'];
				}
				natsort($immeubles);
		?>
		<li id="immeubles-rue-<?php $rue; ?>">
			<button class="voirRue gris" data-rue="<?php echo $rue; ?>" data-nom="<?php echo $r; ?>">Consulter</button>
			<span><?php echo $r; ?></span>
			<span><?php $carto->afficherVille($carto->villeParRue($rue)); ?></span>
		</li>
		<?php } ?>
	</ul>
	
	<div class="coteAcote">
		<button class="ajouterRue">Ajouter une rue</button>
		<button class="ajouterBureau">Ajouter un bureau</button>
	</div>
</section>

<?php if ($boitage->nombreImmeubles($mission['mission_id'], 1)) { ?>

<?php } else { ?>
	<section id="boitage-non-commence" class="icone fusee demi droite">
		<h3>La mission n'a pas été commencée.</h3>
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
	<h4 class="nomRue"></h4>
	
	<ul class="form-liste"></ul>
</section>
