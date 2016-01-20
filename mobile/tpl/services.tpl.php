<?php if (User::auth_level() >= 5) : ?>
<ul id="actions">
	<li>
		<a href="<?php Core::goTo('contacts'); ?>">
			<span>&#xe840;</span>
			<p>Fichier contact</p>
		</a>
	</li>
	<li>
		<a href="<?php Core::goTo('reporting'); ?>">
			<span>&#xe90b;</span>
			<p>Reporting mobile</p>
		</a>
	</li>
</ul>
<?php else : ?>
<ul id="actions">
	<li>
		<a href="<?php Core::goTo('boitage'); ?>">
			<span>&#xe84d;</span>
			<p>Boîtage</p>
		</a>
	</li>
	<li>
		<a href="<?php Core::goTo('porte'); ?>">
			<span>&#xe841;</span>
			<p>Porte à porte</p>
		</a>
	</li>
</ul>
<?php endif; ?>