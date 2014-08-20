<?php
	$rue = $carto->rue($_GET['rue']);
	$ville = $carto->ville($rue['commune_id']);
	$departement = $carto->departement($ville['departement_id']);
	$region = $carto->region($departement['region_id']);
?>
<section id="fiche" data-fiche="<?php echo $ville['id']; ?>">
	<header>
		<h2>
			<?php $carto->afficherVille($rue['commune_id']); ?>
			<span><?php $carto->afficherRue($rue['id']); ?></span>
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
			<p><?php echo $nombre; ?> adresse<?php echo ($nombre > 1) ? 's' : ''; ?> d'électeur récoltée<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Téléphones mobiles</span><?php $nombre = $carto->nombreElecteurs('rue', $rue['id'], 'mobile'); ?>
			<p><?php echo $nombre; ?> numéro<?php echo ($nombre > 1) ? 's' : ''; ?> d'électeur récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Téléphones fixes</span><?php $nombre = $carto->nombreElecteurs('rue', $rue['id'], 'telephone'); ?>
			<p><?php echo $nombre; ?> numéro<?php echo ($nombre > 1) ? 's' : ''; ?> d'électeur récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
	</ul>
</section>
<aside>
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'ville', 'ville' => $rue['commune_id'])); ?>">Retour à la ville</a>
	</nav>

	<h6>Accéder aux informations sur les immeubles : <em><?php $carto->afficherRue($rue['id']); ?></em></h6>
	
	<ul class="deuxColonnes petit">
		<li>
			<span class="label-information">Immeubles</span>
			<ul class="listeEncadree" id="listeImmeubles">
				<?php $immeubles = $carto->listeImmeubles($rue['id']); foreach ($immeubles as $immeuble) : ?>
				<a class="nostyle" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'immeuble', 'immeuble' => $immeuble['id'])); ?>">
					<li class="immeuble">
						<strong><?php echo $immeuble['numero']; ?> <?php echo $rue['nom']; ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</aside>