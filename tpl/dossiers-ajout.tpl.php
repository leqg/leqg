<section id="ajout-dossier">
	<h3>Création d'un nouveau dossier</h3>
	<p>La création d'un nouveau dossier vous permet de pouvoir suivre la réalisation d'une tâche ou d'une problématique sur une multitude de fiches contact.</p>
	
	<form id="form-ajout-dossier" method="post" action="ajax-form.php?action=ajout-dossier">
		<ul>
			<li>
				<label for="form-nom">Titre du dossier</label>
				<input type="text" name="nom" id="form-nom">
			</li>
		</ul>
	</form>
	
	<div id="dossiers-deja-existant"></div>
</section>

<script>
	$(document).ready(function() {
		$("#form-nom").change(function(){
			// À chaque fois que ce formulaire change, on charge la fonction AJAX de recherche des dossiers déjà existants
			var valeur = $("#form-nom").val();
			
			$.ajax({
				type: 'POST',
				url: 'ajax-form.php?action=dossiers-existants',
				data: { 'nom' : valeur , 'fiche' : '<?php echo $_GET['id']; ?>' },
				dataType: 'html'
			}).done(function(data){
				$('#dossiers-deja-existant').html(data);
			});
		});
	});
</script>