<?php if ($user->user['auth'] >= 5) : ?>
<ul id="actions">
	<li>
		<a href="<?php $core->tpl_go_to('recherche', array('destination' => 'interaction')); ?>">
			<span>&#xe80b;</span>
			<p>Nouvelle interaction</p>
		</a>
	</li>
	<li>
		<a href="<?php $core->tpl_go_to('recherche', array('destination' => 'fiche')); ?>">
			<span>&#xe840;</span>
			<p>Consulter une fiche</p>
		</a>
	</li>
</ul>
<?php else : ?>

<?php endif; ?>