<?php
    User::protection(5);
    Core::loadHeader();
?>
<ul id="actions">
	<li>
		<a href="<?php Core::goPage('recherche', array('destination' => 'interaction')); ?>">
			<span>&#xe80b;</span>
			<p>Nouvelle interaction</p>
		</a>
	</li>
	<li>
		<a href="<?php Core::goPage('recherche', array('destination' => 'fiche')); ?>">
			<span>&#xe840;</span>
			<p>Consulter une fiche</p>
		</a>
	</li>
</ul>
<?php Core::loadFooter(); ?>