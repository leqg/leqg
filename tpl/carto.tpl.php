<?php 
	User::protection(5);
	Core::tpl_header();
?>

	<h2>Module d'exploration cartographique</h2>
	
	<form class="rechercheGlobale rechercheVille" action="#" method="post">
		<span class="search-icon">
			<input type="search" name="recherche" id="rechercheVille" placeholder="Commencez l'exploration en cherchant une ville">
			<span class="annexesRecherche">
				<span class="iconeRecherche"></span>
			</span>
		</span>
	</form>
	
	<section class="resultats invisible">
		<h4>Sélectionnez la ville à explorer</h4>
		
		<ul class="listeCommunes"></ul>
	</section>
	
<?php Core::tpl_footer(); ?>