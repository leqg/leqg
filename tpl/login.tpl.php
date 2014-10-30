<?php $core->tpl_header('login'); ?>
	<form action="<?php $core->tpl_get_url('login'); ?>" method="post">
		<h1><span>Le</span>QG<span>.info</span></h1>
		<ul>
			<li>
				<label for="login">Email de connexion :</label>
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
<?php $core->tpl_footer('login'); ?>