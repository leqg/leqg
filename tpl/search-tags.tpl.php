<div id="recherche-tags">
	<h6>Recherche d'un contact par thématiques</h6>
	<form action="<?php $core->tpl_go_to('rechercher'); ?>" method="post">
		<ul>
			<li style="width: 100%">
				<input type="text" name="tags" id="form-tag" placeholder="mobilités, logement, sport, etc." pattern=".{3,}" autocomplete="off">
				<label for="form-tag">Étiquette recherchée</label>
			</li>
			<li>
				<input class="loupe" type="submit" value="&#xe803;">
			</li>
		</ul>
	</form>
</div>