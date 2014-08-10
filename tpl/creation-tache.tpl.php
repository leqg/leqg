<section id="creation">
	<header class="tache">
		<h2><span>Création d'une nouvelle tâche</span> <span id="titre-tache"></span></h2>
	</header>
	
	<form action="<?php $core->tpl_get_url('creation', 'tache', 'type', 'valid', 'submit'); ?>" method="post">	
		<ul class="infos">
			<li>
				<input type="hidden" name="id" id="id-fiche" value="<?php echo $_GET['id']; ?>">
				<label for="tache" class="textarea">Tâche</label>
				<textarea class="fiche" name="tache" id="tache"></textarea>
			</li>
			<li>
				<label for="deadline">Échéance</label>
				<input class="fiche" type="date" name="deadline" id="deadline" autocomplete="off" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" placeholder="jj/mm/aaaa uniquement">
			</li>
			<li>
				<label for="destinataire">Destinataire</label>
				<label for="destinataire-1" class="checkbox"><input type="checkbox" name="destinataire[]" id="destinataire-1" value="1"> Damien Senger</label>
				<label for="destinataire-2" class="checkbox"><input type="checkbox" name="destinataire[]" id="destinataire-2" value="2"> Compte 2</label>
				<label for="destinataire-3" class="checkbox"><input type="checkbox" name="destinataire[]" id="destinataire-3" value="3"> Compte 3</label>
			</li>
			<li class="button">
				<input type="submit" id="submit" value="Créer la tâche">
			</li>
		</ul>
	</form>
</section>

<script>
	$(document).ready(function(){
		// On créé une fonction qui permet de passer le contenu du champ titre dans le titre du header
		function titreVersHeader() {
			var titre = $("#titre").val();
			$("#titre-tache").html(titre);
		}
		
		// On lance cette fonction au chargement de la page, à chaque ajout d'un caractère et à la sortie du champ
		titreVersHeader();
		$("#titre").keyup(titreVersHeader);
	});
</script>