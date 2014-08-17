<div id="nouvelleInteraction">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id())); ?>">Retour à la fiche</a>
	</nav>
	
	<h6>Ajout d'une nouvelle interaction</h6>
	
	<ul class="ficheInteraction deuxColonnes petit">
		<li><!--
		 --><span class="label-information"><label for="form-type">Type</label></span><!--
		 --><span class="bordure-form">
		 		<label class="selectbox" for="form-type">
			 		<select id="form-type" name="type">
						<option value="contact">Entrevue</option>
						<option value="telephone">Contact téléphonique</option>
						<option value="courrier">Courrier postal</option>
						<option value="email">Courrier électronique</option>
						<option value="autre">Autre</option>
				 	</select>
				</label>
			</span><!--
	 --></li>
		<li><!--
		 --><span class="label-information"><label for="form-date">Date</label></span><!--
		 --><input type="text" id="form-date" name="date" value="<?php echo date('d/m/Y'); ?>" placeholder="jj/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}"><!--
	 --></li>
		<li><!--
		 --><span class="label-information"><label for="form-lieu">Lieu</label></span><!--
		 --><input type="text" id="form-lieu" name="lieu"><!--
	 --></li>
		<li><!--
		 --><span class="label-information"><label for="form-objet">Objet</label></span><!--
		 --><input type="text" id="form-objet" name="objet"><!--
	 --></li>
		<li><!--
		 --><span class="label-information"><label for="form-notes" class="textarea">Notes</label></span><!--
		 --><textarea id="form-notes" name="notes"></textarea><!--
	 --></li>
	 	<li class="submit"><!--
	 	 --><input type="submit" id="sauvegarde" value="Sauvegarder l'interaction"><input type="hidden" id="form-fiche" name="fiche" value="<?php echo $fiche->get_infos('id'); ?>">
	 	</li>
	</ul>
</div>