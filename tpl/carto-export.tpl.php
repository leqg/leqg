<section id="fiche" data-fiche="<?php echo $ville['id']; ?>">
	<header class="export">
		<h2>
			Module d'export de fiches
		</h2>
	</header>
	
	<form action="ajax.php?script=estimation-export" method="post" id="export">
		<input type="hidden" name="ville" value="">
		<input type="hidden" name="rue" value="">
		<input type="hidden" name="immeuble" value="">
		<ul class="deuxColonnes">
			<li id="electeur">
				<span class="label-information"><label>Est électeur</label></span>
				<p class="radioBool">
					<label for="electeur-oui"><input type="radio" name="electeur" id="electeur-oui" value="1">&nbsp;Nécessaire</label>
					<label for="electeur-non"><input type="radio" name="electeur" id="electeur-non" value="0" checked>&nbsp;Indifférent</label>
				</p>
			</li>
			<li id="selectionSexe">
				<span class="label-information"><label for="form-sexe">Sexe</label></span>
				<span class="bordure-form"><label class="selectbox"><select name="sexe" id="form-sexe"><option value="i">Indifférent</option><option value="m">Homme</option><option value="f">Femme</opion></select></label></span>
			</li>
			<li id="selectionAgeMin">
				<span class="label-information"><label for="form-age-min">&Acirc;ge minimal</label></span>
				<span class="bordure-form"><label class="selectbox"><select name="age-min" id="form-age-min"><option value="0">Indifférent</option><?php $age = 18; while ($age <= 100) { ?><option value="<?php echo $age; ?>"><?php echo $age; ?> ans</option><?php $age++; } ?></select></label></span>
			</li>
			<li id="selectionAgeMax">
				<span class="label-information"><label for="form-age-max">&Acirc;ge maximal</label></span>
				<span class="bordure-form"><label class="selectbox"><select name="age-max" id="form-age-max"><option value="0">Indifférent</option><?php $age = 18; while ($age <= 100) { ?><option value="<?php echo $age; ?>"><?php echo $age; ?> ans</option><?php $age++; } ?></select></label></span>
			</li>
			<li id="emailConnu">
				<span class="label-information"><label>Email connu</label></span>
				<p class="radioBool">
					<label for="email-oui"><input type="radio" name="email" id="email-oui" value="1">&nbsp;Nécessaire</label>
					<label for="email-non"><input type="radio" name="email" id="email-non" value="0" checked>&nbsp;Indifférent</label>
				</p>
			</li>
			<li id="mobileConnu">
				<span class="label-information"><label>Mobile connu</label></span>
				<p class="radioBool">
					<label for="mobile-oui"><input type="radio" name="mobile" id="mobile-oui" value="1">&nbsp;Nécessaire</label>
					<label for="mobile-non"><input type="radio" name="mobile" id="mobile-non" value="0" checked>&nbsp;Indifférent</label>
				</p>
			</li>
			<li id="fixeConnu">
				<span class="label-information"><label>Fixe connu</label></span>
				<p class="radioBool">
					<label for="fixe-oui"><input type="radio" name="fixe" id="fixe-oui" value="1">&nbsp;Nécessaire</label>
					<label for="fixe-non"><input type="radio" name="fixe" id="fixe-non" value="0" checked>&nbsp;Indifférent</label>
				</p>
			</li>
			<li>
				<span class="label-information">Critère géographique</span>
				<p><a class="nostyle bouton boutonOrange" href="#">Choisir</a><em id="critereGeographique" style="padding-left: 1em"></em></p>
			</li>
			<li class="submit">
				<input type="submit" value="Estimer le nombre de fiches">
			</li>
		</ul>
	</form>
</section>

<aside>
	<div id="estimation">
		<h6>Estimation du nombre de fiches ciblées</h6>
		<p>
			D'après les critères sélectionnés, <strong id="affichageEstimation">0</strong> fiches seront exportées.
		</p>
		<p style="text-align: center;" id="boutonExportation">
			<a href="ajax.php?script=export" id="exportation" class="nostyle bouton boutonRouge">Exporter les fiches</a>
		</p>
		<p id="fichier"><a href="#" target="_blank" class="nostyle bouton boutonVert" style="margin-top: 2em;">Télécharger le fichier</a></p>
		<div id="calcul"><p><span><span>&#xe8eb;</span></span>Calcul en cours</p></div>
	</div>
</aside>