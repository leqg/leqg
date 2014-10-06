<h2>Boîtage</h2>
<?php if ($boitage->nombre() > 0) : ?>

<?php else : ?>
	<section class="icone" id="aucuneMission">
		<h3>Aucune mission lancée actuellement !</h3>
		<a class="nostyle" href="<?php $core->tpl_go_to('boite', array('action' => 'nouveau')); ?>"><button>Créer une mission</button></a>
	</section>
<?php endif; ?>
