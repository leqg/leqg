<?php
    User::protection(5);
    Core::tpl_header();
?>
<ul id="actions">
	<li>
		<a href="<?php Core::tpl_go_to('recherche', array('destination' => 'interaction')); ?>">
			<span>&#xe80b;</span>
			<p>Nouvelle interaction</p>
		</a>
	</li>
	<li>
		<a href="<?php Core::tpl_go_to('recherche', array('destination' => 'fiche')); ?>">
			<span>&#xe840;</span>
			<p>Consulter une fiche</p>
		</a>
	</li>
</ul>
<?php Core::tpl_footer(); ?>