<?php $dossier = $fiche->dossier($_GET['id']); ?>
<section id="fiche-dossier">
	<header>
		<h2><span>Dossier</span><span id="titre-dossier"><?php echo $dossier['nom']; ?></span></h2>
	</header>
	
	<form action="<?php $core->tpl_get_url('modification', 'dossier', 'type', $_GET['id'], 'id'); ?>" method="post">
		<ul class="infos">
			<li>
				<label class="textarea" for="description">Description</label>
				<textarea id="description" name="description"><?php echo $dossier['description']; ?></textarea>
			</li>
		</ul>
	</form>
	
	<h3>Contacts associés au dossier</h3>
	<section class="liste"><?php $fiches = explode(',', $dossier['contacts']); foreach ($fiches as $contact) : ?>
		<a href="<?php $core->tpl_get_url('fiche', $contact); ?>">
			<article class="fiche" data-fiche="<?php echo $contact; ?>">
				<header>
					<a href="<?php $core->tpl_get_url('dossier', 'suppression', 'action', $_GET['id'] . '-' . $contact, 'id'); ?>" class="suppression">&#xe814;</a>
					<h3><?php $fiche->nomByID($contact, 'span', false); ?></h3>
				</header>
			</article>
		</a>
		<?php endforeach; ?>
		<article id="ajout-contact" class="fiche ajout"><header><h3><span>Ajouter</span> <span>un contact</span></h3></header></article>
	</section>
</section>

<aside class="barreGauche">
	<!-- Barre de navigation -->
	<ul id="navigation-aside"><!--
	 --><li class="nav-aside-go" id="nav-aside-taches" data-tab="start">Tâches associées</li><!--
	 --><li class="nav-aside-go" id="nav-aside-fichiers" data-tab="taches">Fichiers associés</li><!--
	 --><li class="nav-aside-go" id="nav-aside-historique" data-tab="historique">Historique</li><!--
 --></ul>
 
 	<div id="aside-taches" class="volet"></div>
 	<div id="aside-fichiers" class="volet"></div>
 	<div id="aside-historique" class="volet"></div>
 	<div id="aside-contacts" class="volet">
	 	<h6>Ajout d'un contact</h6>
	 	<input type="text" id="recherche" placeholder="Entrez le nom et le prénom de la personne à chercher (3 signes min.)">
	 	
	 	<ul id="liste-contacts"></ul>
 	</div>

</aside>

<script>
$(document).ready(function(){
	// Au démarrage, on cache les volets du aside sauf aside-taches
	$(".volet").hide();
	$("#aside-taches").show();
	
	// On ferme tous les volets sauf contacts au clic sur l'ajout d'une fiche
	$("#ajout-contact").click(function(){
		$(".volet").hide();
		$("#aside-contacts").show();
		$("#recherche").focus();
	});

	// Fonction permettant de recherche à partir du moment où l'on tape quelque chose dans le formulaire des fiches correspondantes
	function rechercheFiches() {
		var recherche = $("#recherche").val();
		var exclusion = '<?php echo $dossier['contacts']; ?>';
		
		// On continue si c'est assez long
		if (recherche.length > 3) {
			$.ajax({
				type: 'POST',
				url: 'ajax-form.php?action=recherche-fiche-creation',
				data: { 'id': <?php echo $_GET['id']; ?> , 'recherche': recherche , 'exclusion': exclusion },
				dataType: 'html'
			}).done(function(data){
				$("#liste-contacts").html(data);
			});
		}
	}
	
	rechercheFiches();
	$("#recherche").keyup(rechercheFiches);
});
</script>