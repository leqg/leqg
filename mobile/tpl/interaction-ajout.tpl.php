<h2>Ajout d'une interaction</h2>

<form action="ajax.php?script=interaction-ajout" method="post">
	<input type="hidden" name="fiche" value="<?php echo $_GET['fiche']; ?>">
	<ul class="formulaire">
		<li>
			<label for="form-fiche">Fiche concernée</label>
			<input type="text" name="form-fiche" id="form-fiche" value="<?php $fiche->nomByID($_GET['fiche']); ?>" disabled>
		</li>
		<li>
			<label for="form-type">Type</label>
			<label class="selectbox" for="form-type">
				<select name="type" id="form-type">
					<option value="contact"><?php $historique->returnType('contact'); ?></option>
					<option value="telephone"><?php $historique->returnType('telephone'); ?></option>
					<option value="email"><?php $historique->returnType('email'); ?></option>
					<option value="courrier"><?php $historique->returnType('courrier'); ?></option>
					<option value="autre"><?php $historique->returnType('autre'); ?></option>
				</select>
			</label>
		</li>
		<li>
			<label for="form-date">Date</label>
			<input type="date" name="date" id="form-date" placeholder="aaaa-mm-jj">
		</li>
		<li>
			<label for="form-lieu">Lieu</label>
			<input type="text" name="lieu" id="form-lieu">
		</li>
		<li>
			<label for="form-objet">Objet</label>
			<input type="text" name="objet" id="form-objet">
		</li>
		<li>
			<label for="form-notes">Notes</label>
			<textarea name="notes" id="form-notes"></textarea>
		</li>
		<li>
			<input type="submit" value="Sauvegarder">
		</li>
	</ul>