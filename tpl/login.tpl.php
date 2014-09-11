<?php $core->tpl_header('login'); ?>
	<form action="<?php $core->tpl_get_url('login'); ?>" method="post">
		<ul>
			<li>
				<label for="login">Nom d'utilisateur :</label>
				<input type="text" name="login" id="login" autocomplete="off" autofocus="on">
			</li>
			<li>
				<label for="pass">Mot de passe :</label>
				<input type="password" name="pass" id="pass">
			</li>
			<li class="submit">
				<input type="submit" value="Se connecter">
			</li>
		</ul>
	</form>
	
	<?php if (isset($_GET['erreur']) && $_GET['erreur'] == 'login') { ?><div id="erreur">Erreur login</div><?php } ?>
	<?php if (isset($_GET['erreur']) && $_GET['erreur'] == 'pass') { ?><div id="erreur">Erreur pass</div><?php } ?>
<?php $core->tpl_footer('login'); ?>