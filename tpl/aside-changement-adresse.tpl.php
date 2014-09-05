<div id="changementAdresse">
	<?php if (isset($_GET['modifierAdresse'])) : ?>
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id())); ?>">Retour à la fiche</a>
	</nav>
	<h6>Modification de l'adresse déclarée</h6>
	
	<ul class="deuxColonnes petit">
		<li>
			<span class="label-information">Ville</span>
			<input type="text" name="rechercheVille" id="changementAdresse-rechercheVille" data-fiche="<?php echo $_GET['id']; ?>">
		</li>
	</ul>
	<ul id="liste-villes" class="listeEncadree"></ul>
	<?php endif; ?>
</div>
<div id="changementRue">
	<?php if (isset($_GET['modifierRue'])) : ?>
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id(), 'modifierAdresse' => 'true')); ?>">Retour à la sélection de la ville</a>
	</nav>
	<h6>Modification de l'adresse déclarée</h6>
	
	<ul class="deuxColonnes petit">
		<li>
			<span class="label-information">Ville</span>
			<ul class="listeEncadree"><li class="ville"><?php echo $carto->afficherVille($_GET['ville']); ?></li></ul>
		</li>
		<li>	
			<span class="label-information">Rue</span>
			<input type="text" name="rechercheRue" id="changementAdresse-rechercheRue" data-fiche="<?php echo $_GET['id']; ?>" data-ville="<?php echo $_GET['ville']; ?>">
		</li>
	</ul>
	<ul id="liste-rues" class="listeEncadree" data-ville="<?php echo $ville; ?>" data-fiche="<?php echo $_POST['fiche']; ?>"></ul>
	</ul>
	<?php endif; ?>
</div>

<div id="changementImmeuble">
	<?php if (isset($_GET['modifierImmeuble'])) : ?>
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id(), 'modifierRue' => 'true', 'ville' => $_GET['ville'])); ?>">Retour à la sélection de la rue</a>
	</nav>
	<h6>Modification de l'adresse déclarée</h6>
	
	<ul class="deuxColonnes petit">
		<li>
			<span class="label-information">Ville</span>
			<ul class="listeEncadree"><li class="ville"><?php echo $carto->afficherVille($_GET['ville']); ?></li></ul>
		</li>
		<li>
			<span class="label-information">Rue</span>
			<ul class="listeEncadree"><li class="rue"><?php echo $carto->afficherRue($_GET['rue']); ?></li></ul>
		</li>
		<li>
			<span class="label-information">Immeuble</span>
			<ul id="liste-rues" class="listeEncadree">
				<a href="<?php $core->tpl_go_to('fiche', array('id' => $_GET['id'], 'creerImmeuble' => 'true', 'rue' => $_GET['rue'], 'ville' => $_GET['ville'])); ?>" class="nostyle" id="ajoutImmeuble">
					<li class="immeuble ajoutImmeuble">
						<strong>Créer une nouvelle adresse dans la rue</strong>
					</li>
				</a>
				<?php $immeubles = $carto->listeImmeubles($_GET['rue']); foreach ($immeubles as $immeuble) : ?>
				<a href="ajax.php?script=modifier-adresse&immeuble=<?php echo $immeuble['id']; ?>&fiche=<?php echo $_GET['id']; ?>">
					<li class="immeuble">
						<strong><?php echo $immeuble['numero']; ?> <?php echo $carto->afficherRue($immeuble['rue_id']); ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
	<?php endif; ?>
</div>

<div id="creerImmeuble">
	<?php if (isset($_GET['creerImmeuble'])) : ?>
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id(), 'modifierImmeuble' => 'true', 'rue' => $_GET['rue'], 'ville' => $_GET['ville'])); ?>">Retour à la sélection du numéro</a>
	</nav>
	<h6>Modification de l'adresse déclarée</h6>
	
	<form action="ajax.php?script=creer-immeuble" method="post">
		<input type="hidden" name="ville" value="<?php echo $_GET['ville']; ?>">
		<input type="hidden" name="rue" value="<?php echo $_GET['rue']; ?>">
		<input type="hidden" name="fiche" value="<?php echo $_GET['id']; ?>">
		<ul class="deuxColonnes petit">
			<li>
				<span class="label-information">Ville</span>
				<ul class="listeEncadree"><li class="ville"><?php echo $carto->afficherVille($_GET['ville']); ?></li></ul>
			</li>
			<li>
				<span class="label-information">Rue</span>
				<ul class="listeEncadree"><li class="rue"><?php echo $carto->afficherRue($_GET['rue']); ?></li></ul>
			</li>
			<li>
				<span class="label-information">Numéro</span>
				<input type="text" id="form-immeuble" name="immeuble" placeholder="Numéro de l'immeuble">
			</li>
			<li class="submit">
				<input type="submit" value="Enregistrer l'adresse">
			</li>
		</ul>
	</form>
	<?php endif; ?>
</div>
