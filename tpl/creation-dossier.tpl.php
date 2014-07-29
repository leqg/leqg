<section id="creation">
	<header class="dossier">
		<h2><span>Création d'un nouveau dossier</span> <span id="titre-dossier"></span></h2>
	</header>
	
	<ul class="infos">
		<li>
			<label for="titre">Titre du dossier</label>
			<input type="hidden" name="id" id="id-fiche" value="<?php echo $_GET['id']; ?>">
			<input class="fiche" type="text" name="titre" id="titre">
		</li>
		<li>
			<label for="description" class="textarea">Description</label>
			<textarea class="fiche" name="description" id="description"></textarea>
		</li>
		<li class="button">
			<input type="button" id="finEtape1" value="Passer à l'étape suivante">
		</li>
	</ul>
	
	<!--<div id="ajoutProfils">
		<h3 style="width: 100%;">Liste des fiches associées au dossier</h3>
		<input type="hidden" id="fichesDansDossier" value="<?php echo $_GET['id']; ?>">
		<section class="liste" id="listeFiches" style="width: 100%; margin-left: 3em; margin-right: 3em;">
			<article class="fiche" id="fiche-<?php echo $_GET['id']; ?>">
				<header><h3><?php $fiche->nomByID($_GET['id'], 'span'); ?></h3></header>
			</article>
		</section>
		<ul class="infos">
			<li>
				<label for="recherche">Fiches à ajouter</label>
				<input type="text" class="fiche" name="recherche" id="recherche" placeholder="Recherchez un nom puis cliquez sur la fiche pour l'ajouter">
			</li>
		</ul>
		<section id="result-recherche" class="liste" style="width: 100%; margin-left: 3em; margin-right: 3em;"></section>
	</div>-->
</section>

<script>
/*	$(document).ready(function(){
		// Au démarrage, on enlève de l'affichage ce qui ne doit pas l'être
		$("#ajoutProfils").show();
	
		// On créé une fonction qui permet de passer le contenu du champ titre dans le titre du header
		function titreVersHeader() {
			var titre = $("#titre").val();
			$("#titre-dossier").html(titre);
		}
		
		// On lance cette fonction au chargement de la page, à chaque ajout d'un caractère et à la sortie du champ
		titreVersHeader();
		$("#titre").keyup(titreVersHeader);
		
		/*
		// On enregistre dans la base de données le dossier au moment du passage à l'étape 2
		$("#finEtape1").click(function(){
			// On récupère les données des formulaires
			var titre = $("#titre").val();
			var description = $("#description").val();
			var id = $("#id-fiche").val();
			
			// On lance l'appel AJAX pour enregistrer cela dans la base de données
			$.ajax({
				type: 'POST',
				url: 'ajax-form.php?action=creation-dossier-etape1',
				data: { 'id' : id , 'titre' : titre , 'description' : description },
				dataType: 'html'
			}).done(function(html){
				// On ajout l'ID reçu dans les data attributs de la balise DIV
				$("#ajoutProfils").data('dossier', html);
				
				// On rajoute la page d'origine dans le dossier en cours
				$("#fichesDansDossier").val(id);
				$(".fichesAjoutees").html(liste);
				
				// Quand c'est réussi, on affiche le bloc d'ajout de profils
				$("#ajoutProfils").show();
			});
		});
		
		// On paramètre la fonction de recherche en AJAX de fiches dans la base de données
		function rechercheFiche() {
			var recherche = $("#recherche").val();
			var fichesDansDossier = $("#fichesDansDossier").val();
			
			if (recherche.length > 3) {
				$.ajax({
					type: 'POST',
					url: 'ajax-form.php?action=recherche-fiche-creation',
					data: { 'recherche' : recherche, 'fichesDansDossier' : fichesDansDossier },
					dataType: 'html'
				}).done(function(html){
					// On affiche le rendu
					$("#result-recherche").html(html);
				});
			}
		}
		
		// On effectue une recherche de fiches dès qu'une information est entrée dans la recherche
		rechercheFiche();
		$("#recherche").keyup(rechercheFiche);
		$("#recherche").blur(rechercheFiche);
		*/
/*	});*/
</script>