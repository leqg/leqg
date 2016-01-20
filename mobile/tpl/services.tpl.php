<?php if (User::authLevel() >= 5) : ?>
<ul id="actions">
	<li>
		<a href="<?php Core::goPage('contacts'); ?>">
			<span>&#xe840;</span>
			<p>Fichier contact</p>
		</a>
	</li>
	<li>
		<a href="<?php Core::goPage('reporting'); ?>">
			<span>&#xe90b;</span>
			<p>Reporting mobile</p>
		</a>
	</li>
</ul>
<?php else : ?>
<ul id="actions">
	<li>
		<a href="<?php Core::goPage('boitage'); ?>">
			<span>&#xe84d;</span>
			<p>Boîtage</p>
		</a>
	</li>
	<li>
		<a href="<?php Core::goPage('porte'); ?>">
			<span>&#xe841;</span>
			<p>Porte à porte</p>
		</a>
	</li>
</ul>
<?php endif; ?>