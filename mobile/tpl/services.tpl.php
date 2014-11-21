<?php if (User::auth_level() >= 5) : ?>
<ul id="actions">
	<li>
		<a href="<?php Core::tpl_go_to('contacts'); ?>">
			<span>&#xe840;</span>
			<p>Fichier contact</p>
		</a>
	</li>
	<li>
		<a href="<?php Core::tpl_go_to('reporting'); ?>">
			<span>&#xe90b;</span>
			<p>Reporting mobile</p>
		</a>
	</li>
</ul>
<?php else : ?>
<ul id="actions">
	<li>
		<a href="<?php Core::tpl_go_to('boitage'); ?>">
			<span>&#xe84d;</span>
			<p>Boîtage</p>
		</a>
	</li>
	<li>
		<a href="<?php Core::tpl_go_to('porte'); ?>">
			<span>&#xe841;</span>
			<p>Porte à porte</p>
		</a>
	</li>
</ul>
<?php endif; ?>