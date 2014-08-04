<div id="nouvelleInteraction">
	<h6>Ajout d'une nouvelle interaction</h6>
	
	<ul class="ficheInteraction">
		<li><!--
		 --><label for="form-type">Type</label><!--
		 --><select id="form-type" name="type">
				<option value="contact">Entrevue</option>
				<option value="telephone">Contact téléphonique</option>
				<option value="courrier">Courrier postal</option>
				<option value="email">Courrier électronique</option>
				<option value="autre">Autre</option>
		 	</select><!--
	 --></li>
		<li><!--
		 --><label for="form-date">Date</label><!--
		 --><input type="text" id="form-date" name="date" value="<?php echo date('d/m/Y'); ?>" placeholder="jj/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}"><!--
	 --></li>
		<li><!--
		 --><label for="form-lieu">Lieu</label><!--
		 --><input type="text" id="form-lieu" name="lieu"><!--
	 --></li>
		<li><!--
		 --><label for="form-thema">Thématiques</label><!--
		 --><input type="text" id="form-thema" name="objet" placeholder="séparées par des virgules (logement, sport, etc.)"><!--
	 --></li>
		<li><!--
		 --><label for="form-notes" class="textarea">Notes</label><!--
		 --><textarea id="form-notes" name="notes"></textarea><!--
	 --></li>
	 	<li class="submit"><!--
	 	 --><input type="submit" value="Sauvegarder l'interaction">
	</ul>
</div>