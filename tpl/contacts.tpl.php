<?php Core::tpl_header(); ?>

	<h2>Votre fichier contacts consolidé</h2>
	
	<form class="rechercheGlobale" action="index.php?page=recherche" method="post">
		<span class="search-icon">
			<input type="search" name="recherche" placeholder="Recherche de fiche">
			<span class="annexesRecherche">
				<span class="iconeRecherche"></span>
				<input type="submit" class="lancementRecherche" value="&#xe8af;">
			</span>
		</span>
	</form>
	
	<div class="colonne demi gauche">
		<section class="contenu demi">
			<ul class="iconesActions">
				<a href="<?php Core::tpl_go_to('contact', array('operation' => 'creation')); ?>"><li class="new">Nouvelle fiche</li></a>
				<a href="<?php Core::tpl_go_to('fiche', array('operation' => 'fusion')); ?>"><li class="merge">Fusion de fiches</li></a>
			</ul>
		</section>
		
		<section class="contenu demi">
			<h4>Critères de tri des fiches</h4>
			<input type="hidden" name="tri" id="tri" value="">
			
			<ul class="listeTris">
				<li class="tri ajoutTri">Ajout d'un critère de tri</li>
			</ul>
		</section>
	</div>
	
	<div class="colonne demi droite">
		<section class="contenu demi">
			<h3 class="manqueCritere">Indiquez un critère pour lancer le tri</h3>
		</section>
	</div>

<?php Core::tpl_footer(); ?>