<?php
	$rue = $carto->rue($_GET['rue']);
	$ville = $carto->ville($rue['commune_id']);
	$departement = $carto->departement($ville['departement_id']);
	$region = $carto->region($departement['region_id']);
?>
<section id="fiche" data-fiche="<?php echo $ville['id']; ?>">
	<header class="rue">
		<h2>
			<?php $carto->afficherVille($rue['commune_id']); ?>
			<span><?php $carto->afficherRue($rue['id']); ?></span>
			<a href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'rue', 'rue' => $rue['id'], 'modifier' => 'informations')); ?>" class="nostyle" id="config-icon">&#xe855;</a>
		</h2>
	</header>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Région</span>
			<p><?php echo $region['nom']; ?></p>
		</li>
		<li>
			<span class="label-information">Département</span>
			<p><?php echo $departement['nom']; ?> (<?php echo $departement['id']; ?>)</p>
		</li>
		<li>
			<span class="label-information">Électeurs</span><?php $nombre = $carto->nombreElecteurs('rue', $rue['id']); ?>
			<p><?php echo $nombre; ?> électeur<?php echo ($nombre > 1) ? 's' : ''; ?> connu<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Emails</span><?php $nombre = $carto->nombreElecteurs('rue', $rue['id'], 'email'); ?>
			<p><?php echo $nombre; ?> contact<?php echo ($nombre > 1) ? 's' : ''; ?> récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Téléphones mobiles</span><?php $nombre = $carto->nombreElecteurs('rue', $rue['id'], 'mobile'); ?>
			<p><?php echo $nombre; ?> contact<?php echo ($nombre > 1) ? 's' : ''; ?> récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Téléphones fixes</span><?php $nombre = $carto->nombreElecteurs('rue', $rue['id'], 'telephone'); ?>
			<p><?php echo $nombre; ?> contact<?php echo ($nombre > 1) ? 's' : ''; ?> récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
	</ul>
</section>
<aside>
	<?php if (isset($_GET['modifier']) && $_GET['modifier'] == 'informations') : ?>
	<div>
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'rue', 'rue' => $rue['id'])); ?>">Retour aux immeubles</a>
		</nav>
		
		<h6>Modifier le nom de la rue</h6>
		
		<form method="post" action="ajax.php?script=rue-changement-nom">
			<input type="hidden" name="rue" value="<?php echo $rue['id']; ?>">
			<ul class="deuxColonnes">
				<li>
					<span class="label-information"><label for="nouveau-nom">Nom actuel</label></span>
					<p><?php echo $rue['nom']; ?></p>
				</li>
				<li>
					<span class="label-information"><label for="nouveau-nom">Nouveau nom</label></span>
					<input type="text" id="nouveau-nom" name="nom" placeholder="Rue des acacias" value="<?php echo $rue['nom']; ?>">
				</li>
				<li class="submit">
					<input type="submit" value="Valider les changements">
				</li>
			</ul>
		</form>
	</div>
	<?php else : ?>
	<div>
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'ville', 'ville' => $rue['commune_id'])); ?>">Retour à la ville</a>
		</nav>
	
		<h6>Accéder aux informations sur les immeubles : <em><?php $carto->afficherRue($rue['id']); ?></em></h6>
		
		<ul class="listeEncadree" id="listeImmeubles">
			<?php $immeubles = $carto->listeImmeubles($rue['id']); foreach ($immeubles as $immeuble) : $coordonnees = $carto->coordonneesDansImmeuble($immeuble['id']); ?>
			<a class="nostyle" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'immeuble', 'immeuble' => $immeuble['id'])); ?>">
				<li class="immeuble <?php if ($coordonnees) echo 'coordonnees'; ?>">
					<strong><?php echo $immeuble['numero']; ?> <?php echo $rue['nom']; ?></strong>
				</li>
			</a>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
</aside>