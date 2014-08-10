<section id="creation">
	<header class="dossier">
		<h2><span>Création d'un nouveau dossier</span> <span id="titre-dossier"></span></h2>
	</header>
	
	<form action="<?php $core->tpl_get_url('creation', 'dossier', 'type', 'valid', 'submit'); ?>" method="post">	
		<ul class="infos">
			<li>
				<label for="titre">Titre du dossier</label>
				<input type="hidden" name="id" id="id-fiche" value="<?php echo $_GET['id']; ?>">
				<input class="fiche" type="text" name="titre" id="titre" autocomplete="off">
			</li>
			<li>
				<label for="description" class="textarea">Description</label>
				<textarea class="fiche" name="description" id="description"></textarea>
			</li>
			<li class="button">
				<input type="submit" id="submit" value="Créer le dossier">
			</li>
		</ul>
	</form>
</section>

<script>
	$(document).ready(function(){
		// On créé une fonction qui permet de passer le contenu du champ titre dans le titre du header
		function titreVersHeader() {
			var titre = $("#titre").val();
			$("#titre-dossier").html(titre);
		}
		
		// On lance cette fonction au chargement de la page, à chaque ajout d'un caractère et à la sortie du champ
		titreVersHeader();
		$("#titre").keyup(titreVersHeader);
	});
</script>