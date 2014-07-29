<section id="creation">
	<header>
		<h2><span>Création d'un</span> <span>nouveau dossier</span></h2>
	</header>
	
	<ul class="infos">
		<li>
			<label for="titre">Titre du dossier</label>
			<input class="fiche" type="text" name="titre" id="titre">
		</li>
		<li>
			<label for="description">Description</label>
			<input class="fiche" type="text" name="description" id="description">
		</li>
		<li>
			<label for="fiche">Fiches liées</label>
			<input class="fiche" type="text" name="fiche" id="fiche"> <button>Ajouter la fiche</button>
			<input type="hidden" name="fiches" id="fiches">
			<div id="liste-fiches"></div>
		</li>
	</ul>
</section>