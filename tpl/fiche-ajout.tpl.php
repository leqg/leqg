<div id="creationNouvelleFiche">
	<section id="fiche-electeur">
		<header>
			<h2 class="titre">Création d'une fiche</h2>
		</header>
		
		<ul class="ficheInteraction creation deuxColonnes">
			<li>
				<span class="label-information"><label for="form-creation-nom">Nom</label></span>
				<input type="text" name="nom" id="form-creation-nom">
			</li>
			<li>
				<span class="label-information"><label for="form-creation-nom-usage">Nom d'usage</label></span>
				<input type="text" name="nom-usage" id="form-creation-nom-usage">
			</li>
			<li>
				<span class="label-information"><label for="form-creation-prenom">Prénoms</label></span>
				<input type="text" name="prenom" id="form-creation-prenom">
			</li>
			<li>
				<span class="label-information"><label for="form-sexe">Sexe</label></span>
				<span class="bordure-form"><label class="selectbox"><select name="sexe" id="form-sexe"><option value="I">Inconnu</option><option value="M">Homme</option><option value="F">Femme</opion></select></label></span>
			</li>
			<li>
				<span class="label-information"><label for="form-fixe">Téléphone fixe</label></span>
				<input type="text" name="fixe" id="form-fixe" placeholder="00 00 00 00 00" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$">
			</li>
			<li>
				<span class="label-information"><label for="form-mobile">Téléphone mobile</label></span>
				<input type="text" name="mobile" id="form-mobile" placeholder="00 00 00 00 00" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$">
			</li>
			<li>
				<span class="label-information"><label for="form-email">Adresse email</label></span>
				<input type="email" name="email" id="form-email" placeholder="abc@domaine.com">
			</li>
			<li class="submit">
				<input type="submit" id="creerFiche" value="Créer la fiche">
			</li>
		</ul>
	</section>
</div>