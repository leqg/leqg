<?php $core->tpl_header(); ?>
	<h2>Missions de porte-à-porte</h2>

	<ul class="listeMissions">
		<?php if ($porte->nombre() > 0) : ?>
		<?php $missions = $porte->missions(); foreach ($missions as $mission) : ?>
		<a href="<?php $core->tpl_go_to('porte', array('mission' => md5($mission['mission_id']))); ?>" class="nostyle">
			<li>
				<h4><?php echo $mission['mission_nom']; ?></h4>
				<?php if (!is_null($mission['mission_deadline'])) : ?><p><span>Deadline :</span> <strong><?php echo date('d/m/Y', strtotime($mission['mission_deadline'])); ?></strong></p><?php endif; ?>
			</li>
		</a>
		<?php endforeach; ?>
		<?php else : ?>
		<li class="vide">
			<p>Aucune mission actuellement</p>
		</li>
		<?php endif; ?>
	</ul>
<?php $core->tpl_footer(); ?>