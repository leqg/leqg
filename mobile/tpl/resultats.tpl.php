<li class="electeur">
	<?php if (isset($_GET['destination']) && $_GET['destination'] == 'interaction') : ?>
		<a href="<?php $core->tpl_go_to('interaction', array('action' => 'ajout', 'fiche' => $fiche->get_the_ID())); ?>" class="nostyle">
	<?php else: ?> 
		<a href="<?php $core->tpl_go_to('contacts', array('fiche' => $fiche->get_the_ID())); ?>" class="nostyle">
	<?php endif; ?>
		<strong><?php $fiche->affichage_nom(); ?></strong>
	</a>
</li>