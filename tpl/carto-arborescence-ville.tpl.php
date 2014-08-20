<?php
	$ville = $carto->ville($_GET['ville']);
	$departement = $carto->departement($ville['departement_id']);
	$region = $carto->region($departement['region_id']);
?>
<section id="fiche" data-fiche="<?php echo $ville['id']; ?>">
	<header>
		<h2>
			Ville de
			<span><?php $carto->afficherVille($ville['id']); ?></span>
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
			<span class="label-information">Électeurs</span><?php $nombre = $carto->nombreElecteurs('commune', $ville['id']); ?>
			<p><?php echo $nombre; ?> électeur<?php echo ($nombre > 1) ? 's' : ''; ?> connu<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Emails</span><?php $nombre = $carto->nombreElecteurs('commune', $ville['id'], 'email'); ?>
			<p><?php echo $nombre; ?> adresse<?php echo ($nombre > 1) ? 's' : ''; ?> d'électeur récoltée<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Téléphones mobiles</span><?php $nombre = $carto->nombreElecteurs('commune', $ville['id'], 'mobile'); ?>
			<p><?php echo $nombre; ?> numéro<?php echo ($nombre > 1) ? 's' : ''; ?> d'électeur récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
		<li>
			<span class="label-information">Téléphones fixes</span><?php $nombre = $carto->nombreElecteurs('commune', $ville['id'], 'telephone'); ?>
			<p><?php echo $nombre; ?> numéro<?php echo ($nombre > 1) ? 's' : ''; ?> d'électeur récolté<?php echo ($nombre > 1) ? 's' : ''; ?></p>
		</li>
	</ul>
</section>
<aside>
	<h6>Accéder aux informations sur les rues de <em><?php $carto->afficherVille($ville['id']); ?></em></h6>
	
	<ul class="deuxColonnes petit">
		<li>
			<span class="label-information"><label for="rechercheRue">Recherche</label></span>
			<input type="text" name="recherche" id="rechercheRue" data-ville="<?php echo $ville['id']; ?>">
		</li>
		<li>
			<ul class="listeEncadree" id="listeRues">
				<?php $rues = $carto->listeRues($ville['id']); foreach ($rues as $rue) : ?>
				<a class="nostyle" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'rue', 'rue' => $rue['id'])); ?>">
					<li class="rue">
						<strong><?php echo $rue['nom']; ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</aside>