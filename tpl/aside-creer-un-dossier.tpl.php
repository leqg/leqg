<div id="creerUnDossier">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id(), 'interaction' => $_GET['interaction'])); ?>">Retour à l'interaction</a>
	</nav>
	<h6>Créer un nouveau dossier à partir d'une interaction</h6>
	
	<form action="ajax.php?script=nouveau-dossier-rapide" method="post">
		<input type="hidden" name="fiche" value="<?php echo $_GET['id']; ?>">
		<input type="hidden" name="interaction" value="<?php echo $_GET['interaction']; ?>">
		<ul class="deuxColonnes petit">
			<li>
				<span class="label-information"><label for="form-nom">Nom du dossier</label></span>
				<input type="text" name="nom" id="form-nom">
			</li>
			<li>
				<span class="label-information"><label for="form-description" class="textarea">Description</label></span>
				<textarea id="form-description" name="description"></textarea>
		 	<li class="submit">
		 		<input type="submit" value="Créer le dossier">
		 	</li>
		</ul>
	</form>
</div>