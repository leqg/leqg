<div id="changementEtatCivil">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id())); ?>">Retour à la fiche</a>
	</nav>
	
	<h6>Modification des informations d'état civil</h6>
	
	<form action="ajax.php?script=modifier-etatcivil&fiche=<?php echo $_GET['id']; ?>" method="post">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Nom de naissance</span>
				<input type="text" name="nom" id="changement-nom" value="<?php $fiche->infos('nom'); ?>">
			</li>
			<li>
				<span class="label-information">Nom d'usage</span>
				<input type="text" name="nomUsage" id="changement-nomUsage" value="<?php $fiche->infos('nom_usage'); ?>">
			</li>
			<li>
				<span class="label-information">Prénoms</span>
				<input type="text" name="prenoms" id="changement-prenom" value="<?php $fiche->infos('prenoms'); ?>">
			</li>
			<li class="submit">
				<input type="submit" value="Confirmer ces informations">
			</li>
		</ul>
	</form>
</div>
