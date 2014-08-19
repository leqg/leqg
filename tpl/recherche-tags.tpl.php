<section id="recherche-tags">
	<h6>Recherche par étiquette</h6>
	<form action="<?php $core->tpl_get_url('recherche-tag'); ?>" method="post">
		<ul>
			<li style="width: 100%">
				<input type="text" name="tag" id="form-tag" placeholder="mobilités, logement, sport, etc." pattern=".{3,}" autocomplete="off" list="list-tag">
				<label for="form-prenom" pattern=".{3,}">Étiquette recherchée</label>
			</li><!--
		 --><li>
				<input class="loupe" type="submit" value="&#xe803;">
			</li>
		</ul>
		<datalist id="list-tag">
 	 		<?php
 	 			$query = 'SELECT * FROM tags ORDER BY rand() LIMIT 0,10';
 	 			$sql = $db->query($query);
 	 			while ($row = $sql->fetch_array()) {
	 	 	?>
	 	 	<option value="<?php echo utf8_encode($row[0]); ?>">
 	 		<?php } ?>
		</datalist>
	</form>
</section>