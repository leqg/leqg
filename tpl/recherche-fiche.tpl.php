<div id="recherche-fiche">
	<h6>Recherche de contacts</h6>
	<form action="<?php $core->tpl_get_url('recherche'); ?>" method="post">
		<ul>
			<li>
				<input type="text" name="recherche" id="form-recherche" placeholder="Michel Dupont" pattern=".{3,}" autocomplete="off">
				<label for="form-nom">Nom et Prénoms</label>
			</li><!--
		 --><li>
				<input class="loupe" type="submit" value="&#xe803;">
			</li>
		</ul>
	</form>
</div>