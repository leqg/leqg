<section id="recherche-fiche">
	<h6>Recherche de contacts</h6>
	<form action="<?php $core->tpl_get_url('recherche'); ?>" method="post">
		<ul>
			<li>
				<input type="text" name="nom" id="form-nom" placeholder="Dupont" pattern=".{3,}" autocomplete="off">
				<label for="form-nom">Nom</label>
			</li><!--
		 --><li>
				<input type="text" name="prenom" id="form-prenom" placeholder="Pierre" pattern=".{3,}" autocomplete="off">
				<label for="form-prenom">Prénom</label>
			</li><!--
		 --><li>
				<input type="submit" value="&#xe803;">
			</li>
		</ul>
	</form>
</section>