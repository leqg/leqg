<section id="fiche-electeur">
	<header>
		<h2 class="titre">Création d'une fiche</h2>
	</header>
	
	<ul class="ficheInteraction creation">
		<li>
			<label for="form-creation-nom">Nom</label>
			<input type="text" name="nom" id="form-creation-nom">
		</li>
		<li>
			<label for="form-creation-nom-usage">Nom d'usage</label>
			<input type="text" name="nom-usage" id="form-creation-nom-usage">
		</li>
		<li>
			<label for="form-creation-prenom">Prénoms</label>
			<input type="text" name="prenom" id="form-creation-prenom">
		</li>
		<li>
			<label for="form-sexe">Sexe</label>
			<select name="sexe" id="form-sexe"><option value="I">Inconnu</option><option value="M">Homme</option><option value="F">Femme</opion></select>
		</li>
		<li>
			<label for="form-fixe">Téléphone fixe</label>
			<input type="text" name="fixe" id="form-fixe" placeholder="00 00 00 00 00" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$">
		</li>
		<li>
			<label for="form-mobile">Téléphone mobile</label>
			<input type="text" name="mobile" id="form-mobile" placeholder="00 00 00 00 00" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$">
		</li>
		<li>
			<label for="form-email">Adresse email</label>
			<input type="email" name="email" id="form-email" placeholder="abc@domaine.com">
		</li>
		<li class="submit">
			<input type="submit" id="creerFiche" value="Créer la fiche">
		</li>
	</ul>
	
	<div id="test"></div>
</section>