<?php
	$immeuble = $carto->immeuble($_GET['immeuble']);
	$bureau = $carto->bureau($immeuble['bureau_id']);
	//$canton = $carto->canton($bureau['canton_id']);
	$rue = $carto->rue($immeuble['rue_id']);
	$ville = $carto->ville($rue['commune_id']);
	$departement = $carto->departement($ville['departement_id']);
	$region = $carto->region($departement['region_id']);
?>
<section id="fiche" data-fiche="<?php echo $ville['id']; ?>">
	<header>
		<h2>
			<?php $carto->afficherVille($rue['commune_id']); ?>
			<span><?php echo $immeuble['numero']; ?> <?php $carto->afficherRue($rue['id']); ?></span>
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
		<?php /*if ($canton != null) : ?>
		<li>
			<span class="label-information">Canton</span>
			<p><?php echo $canton['nom']; ?> (<?php echo $canton['id']; ?>)</p>
		</li>
		<?php endif;*/ ?>
		<li>
			<span class="label-information">Électeurs</span><?php $nombre = $carto->nombreElecteurs('immeuble', $immeuble['id']); ?>
			<p><?php echo $nombre; ?> électeur<?php echo ($nombre > 1) ? 's' : ''; ?> connu<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Emails</span><?php $nombre = $carto->nombreElecteurs('immeuble', $immeuble['id'], 'email'); ?>
			<p><?php echo $nombre; ?> contact<?php echo ($nombre > 1) ? 's' : ''; ?> récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Téléphones mobiles</span><?php $nombre = $carto->nombreElecteurs('immeuble', $immeuble['id'], 'mobile'); ?>
			<p><?php echo $nombre; ?> contact<?php echo ($nombre > 1) ? 's' : ''; ?> récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Téléphones fixes</span><?php $nombre = $carto->nombreElecteurs('immeuble', $immeuble['id'], 'telephone'); ?>
			<p><?php echo $nombre; ?> contact<?php echo ($nombre > 1) ? 's' : ''; ?> récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
	</ul>
</section>
<aside>
	<div>
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'rue', 'rue' => $immeuble['rue_id'])); ?>">Retour à la rue</a>
		</nav>
	
		<h6>Accéder aux informations sur les électeurs du <em><?php echo $immeuble['numero']; ?> <?php $carto->afficherRue($rue['id']); ?></em></h6>
		
		<ul class="deuxColonnes petit">
			<li>
				<span class="label-information">Électeurs</span>
				<ul class="listeEncadree" id="listeImmeubles">
					<?php $electeurs = $carto->listeElecteurs($immeuble['id']); foreach ($electeurs as $electeur) : ?>
					<a class="nostyle" href="<?php $core->tpl_go_to('fiche', array('id' => $electeur['id'])); ?>">
						<li class="electeur">
							<?php $fiche->acces($electeur['id'], true); ?>
							<strong><?php echo $fiche->affichage_nom(); ?> (<?php $fiche->age(); ?>)</strong>
						</li>
					</a>
					<?php endforeach; ?>
				</ul>
			</li>
		</ul>
	</div>
</aside>