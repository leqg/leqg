<?php $core->tpl_header(); ?>
	
	<h2>Boîtage</h2>
	<?php if ($boitage->nombre() > 0) : ?>
		<section id="missions">
			<a href="<?php $core->tpl_go_to('boite', array('mission' => md5(1))); ?>">Go!</a>
		</section>
	<?php else : ?>
		<section class="icone" id="aucuneMission">
			<h3>Aucune mission lancée actuellement !</h3>
			<a class="nostyle" href="<?php $core->tpl_go_to('boite', array('action' => 'nouveau')); ?>"><button>Créer une mission</button></a>
		</section>
	<?php endif; ?>
	
<?php $core->tpl_footer(); ?>