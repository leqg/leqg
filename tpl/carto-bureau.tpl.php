<?php $bureau = $carto->bureau($_GET['bureau']); ?>
<section id="fiche">
	<header class="bureau">
		<h2><span>Bureau</span><span id="titre-dossier"><?php echo $bureau['nom']; ?></span></h2>
		<a class="nostyle" id="config-icon" href="<?php $core->tpl_go_to('carto', array('module' => 'bureau', 'bureau' => $bureau['id'], 'modifierInfos' => 'true')); ?>">&#xe855;</a>
	</header>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Lieu</span>
			<p><?php echo $core->tpl_transform_texte($bureau['nom']); ?></p>
		</li>
		<li>
			<span class="label-information">Adresse</span>
			<p><?php echo $core->tpl_transform_texte($bureau['adresse']); ?><br><?php echo $bureau['cp']; ?> <?php $carto->afficherVille($bureau['commune_id']); ?></p>
		</li>
		<li>
			<span class="label-information">Commune</span>
			<p><?php $carto->afficherVille($bureau['commune_id']); ?></p>
			<a class="nostyle icone" title="Accéder à la fiche de présentation de la commune" href="<?php $core->tpl_go_to('carto', array('module' => 'arborescence', 'branche' => 'ville', 'ville' => $bureau['commune_id'], 'liste' => 'bureaux')); ?>">&#xe844;</a>
		</li>
		<li>
			<span class="label-information">Canton</span>
			<p><?php $carto->afficherCanton($bureau['canton_id']); ?></p>
		</li>
		<li>
			<span class="label-information">Recensement</span><?php $electeurs = $carto->nombreElecteurs('bureau', $bureau['id']); ?>
			<p><?php echo $electeurs; ?> électeur<?php if ($electeurs > 1) echo 's'; ?></p>
		</li>
		<li>
			<span class="label-information">Emails collectés</span><?php $emails = $carto->nombreElecteurs('bureau', $bureau['id'], 'email'); ?>
			<p><?php echo $emails; ?> adresse<?php if ($emails > 1) echo 's'; ?> récoltée<?php if ($emails > 1) echo 's'; ?></p>
		</li>
		<li>
			<span class="label-information">Mobiles collectés</span><?php $mobiles = $carto->nombreElecteurs('bureau', $bureau['id'], 'mobile'); ?>
			<p><?php echo $mobiles; ?> numéro<?php if ($mobiles > 1) echo 's'; ?> récolté<?php if ($emails > 1) echo 's'; ?></p>
		</li>
		<li>
			<span class="label-information">Fixes collectés</span><?php $fixes = $carto->nombreElecteurs('bureau', $bureau['id'], 'telephone'); ?>
			<p><?php echo $fixes; ?> numéro<?php if ($fixes > 1) echo 's'; ?> récolté<?php if ($fixes > 1) echo 's'; ?></p>
		</li>
	</ul>
</section>

<aside>
	<div>
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('carto', array('module' => 'bureaux')); ?>">Retour aux bureaux</a>
		</nav>
	</div>
</aside>