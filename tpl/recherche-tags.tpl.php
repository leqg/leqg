<input type="hidden" name="recherche" id="recherche" value="<?php echo $_POST['tags']; ?>">
<table class="rechercheGlobale">
	<tr>
		<td id="resultatsFiches">
			<h3>Fiches trouvées</h3>
			<ul class="listeEncadree" id="fichesTrouvees"><li class="vide"><strong>Recherche en cours…</strong></li></ul>
		</td>
		<td id="resultatsInteractions">
			<h3>Interactions trouvées</h3>
			<ul class="listeEncadree" id="interactionsTrouvees"><li class="vide"><strong>Recherche en cours…</strong></li></ul>
		</td>
	</tr>
	<tr>
		<td id="resultatsDossiers">
			<h3>Dossiers trouvés</h3>
			<ul class="listeEncadree" id="dossiersTrouves"><li class="vide"><strong>Recherche en cours…</strong></li></ul>
		</td>
		<td id="resultatsFichiers">
			<h3>Fichiers trouvés</h3>
			<ul class="listeEncadree" id="fichiersTrouves"><li class="vide"><strong>Recherche en cours…</strong></li></ul>
		</td>
	</tr>
</table>