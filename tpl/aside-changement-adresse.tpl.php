<div id="changementAdresse">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id())); ?>">Retour à la fiche</a>
	</nav>
	<h6>Modification de l'adresse déclarée</h6>
	
	<ul class="deuxColonnes petit">
		<li>
			<span class="label-information">Ville</span>
			<input type="text" name="rechercheVille" id="changementAdresse-rechercheVille" data-fiche="<?php echo $_GET['id']; ?>">
		</li>
		<li id="resultats">
			<span class="label-information">Choisir</span>
			<ul id="liste-villes" class="listeEncadree"></ul>
		</li>
	</ul>
</div>
<div id="changementRue">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id())); ?>&modifierAdresse=true">Retour à la sélection de la ville</a>
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
		<li id="resultatsRue">
			<span class="label-information">Choisir</span>
			<ul id="liste-rues" class="listeEncadree"></ul>
		</li>
	</ul>
</div>

<div id="changementImmeuble">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id())); ?>&modifierRue=true">Retour à la sélection de la rue</a>
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
		<li id="resultatsImmeuble">
			<span class="label-information">Choisir</span>
			<ul id="liste-rues" class="listeEncadree">
				<?php $immeubles = $carto->listeImmeubles($_GET['rue']); foreach ($immeubles as $immeuble) : ?>
				<a href="ajax.php?script=modifier-adresse&immeuble=<?php echo $immeuble['id']; ?>&fiche=<?php echo $_GET['id']; ?>"><li class="immeuble"><strong><?php echo $immeuble['numero']; ?> <?php echo $carto->afficherRue($immeuble['rue_id']); ?></strong></li></a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</div>
