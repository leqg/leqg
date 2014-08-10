<div id="changementAdresse">
	<h6>Modification de l'adresse déclarée</h6>
	<div id="choixVille">
		<label for="rechercheVille">Recherchez la ville, puis sélectionnez la meilleure correspondance</label>
		<input type="text" name="ville" id="rechercheVille">
		<ul id="selectionVille"></ul>
	</div>
	<div id="choixRue">
		<label for="rechercheRue">Recherchez la rue, puis sélectionnez la meilleure correspondance ou créez-en une nouvelle</label>
		<input type="text" name="rue" id="rechercheRue">
		<ul id="selectionRue" data-ville=""></ul>
	</div>
	<div id="choixImmeuble">
		<label for="rechercheImmeuble">Recherchez le numéro ou créez-en un nouveau pour cette rue</label>
		<ul id="selectionImmeuble" data-ville="" data-rue=""></ul>
	</div>
</div>