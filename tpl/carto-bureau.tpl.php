<?php $bureau = $carto->bureau($_GET['bureau']); ?>
<section id="fiche">
	<header class="bureau">
		<h2><span>Bureau <?php echo $bureau['numero']; ?></span><span id="titre-dossier"><?php echo $bureau['nom']; ?></span></h2>
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
			<p><?php if ($bureau['canton_id']) : $carto->afficherCanton($bureau['canton_id']); else : echo 'Aucun canton rattaché au bureau de vote'; endif; ?></p>
			<a class="nostyle icone" title="Modifier le canton rattaché au bureau de vote" href="<?php $core->tpl_go_to('carto', array('module' => 'bureaux', 'bureau' => $bureau['id'], 'modifierCanton' => 'true')); ?>">&#xe855;</a>
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
	<?php if (isset($_GET['modifierInfos'])) : ?>
	<div>
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('carto', array(/*'module' => 'bureaux', 'bureau' => $bureau['id']*/)); ?>">Annuler les modifications</a>
		</nav>
		
		<h6>Modification des informations du bureau de vote</h6>
		
		<form action="ajax.php?script=modifier-infos-bureau" method="post">
			<input type="hidden" name="bureau" value="<?php echo $bureau['id']; ?>">
			<ul class="deuxColonnes">
				<li>
					<span class="label-information"><label for="form-numero">Numéro</label></span>
					<input type="text" name="numero" id="form-numero" value="<?php echo $bureau['numero']; ?>">
				</li>
				<li>
					<span class="label-information"><label for="form-nom">Nom</label></span>
					<input type="text" name="nom" id="form-nom" value="<?php echo trim($core->tpl_transform_texte($bureau['nom'])); ?>">
				</li>
				<li>
					<span class="label-information"><label for="form-adresse">Adresse</label></span>
					<input type="text" name="adresse" id="form-adresse" value="<?php echo trim($core->tpl_transform_texte($bureau['adresse'])); ?>">
				</li>
				<li>
					<span class="label-information"><label for="form-cp">Code postal</label></span>
					<input type="text" name="cp" id="form-cp" value="<?php echo trim($core->tpl_transform_texte($bureau['cp'])); ?>">
				</li>
				<li>
					<span class="label-information">Ville</span>
					<p><?php $carto->afficherVille($bureau['commune_id']); ?></p>
				</li>
				<li class="submit">
					<input type="submit" value="Valider les modifications">
				</li>
			</ul>
		</form>
	</div>
	<?php elseif (isset($_GET['modifierCanton'])) : ?>
	<div>
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('carto', array('module' => 'bureaux', 'bureau' => $bureau['id'])); ?>">Annuler les modifications</a>
		</nav>
		
		<h6>Modification du canton rattaché au bureau de vote</h6>
		
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Recherche du canton</span>
				<input type="text" name="rechercheCanton" id="rechercheCanton" placeholder="e.g. Strasbourg" data-bureau="<?php echo $bureau['id']; ?>">
			</li>
			<li id="resultatsCantons">
				<span class="label-information">Confirmez le canton</span>
				<ul class="listeEncadree" id="listeCantons"></ul>
			</li>
		</ul>
	</div>
	<?php else : ?>
	<div>
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('carto', array('module' => 'bureaux')); ?>">Retour aux bureaux</a>
		</nav>
		
		<h6>Liste des contacts connus dans la base de données</h6>
		
		<ul class="listeEncadree">
			<?php $electeurs = $carto->listeElecteursParBureau($bureau['id'], true); foreach ($electeurs as $electeur) : ?>
			<a href="<?php echo $core->tpl_go_to('fiche', array('id' => $electeur['id'])); ?>">
				<li class="electeur">
					<strong><?php $fiche->nomByID($electeur['id']); ?></strong>
				</li>
			</a>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
</aside>