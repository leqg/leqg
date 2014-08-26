<div id="nouvelleTache">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $_GET['id'], 'interaction' => $_GET['interaction'])); ?>">Retour à l'interaction</a>
	</nav>
	
	<h6>Ajout d'une nouvelle tâche</h6>
	
	<form action="ajax.php?script=nouvelle-tache" method="post">
		<input type="hidden" name="dossier" value="<?php echo $historique->dossierParInteraction($_GET['interaction']); ?>">
		<input type="hidden" name="interaction" value="<?php echo $_GET['interaction']; ?>">
		<input type="hidden" name="fiche" value="<?php echo $_GET['id']; ?>">
		<ul class="ficheInteraction deuxColonnes petit">
			<li>
				<span class="label-information"><label for="form-tache">Tâche</label></span>
				<input type="text" name="description" id="form-tache">
			</li>
			<li>
				<span class="label-information"><label for="form-destinataire">Destinataire</label></span>
				<span class="bordure-form">
			 		<label class="selectbox" for="form-destinataire">
				 		<select id="form-destinataire" name="destinataire">
							<option value="">Tâche non affectée</option>
							<?php $users = $user->liste(); foreach ($users as $u) : ?>
							<option value="<?php echo $u['id']; ?>"><?php echo $user->get_login_by_ID($u['id']); ?></option>
							<?php endforeach; ?>
					 	</select>
					</label>
				</span>
			</li>
			<li>
				<span class="label-information"><label for="form-deadline">Date limite</label></span>
				<input type="text" name="deadline" id="form-deadline" placeholder="jj/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}">
			</li>
			<li class="submit">
				<input type="submit" value="Créer la tâche">
			</li>
		</ul>
	</form>
</div>