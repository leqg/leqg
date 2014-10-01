<?php $usr = $user->infos($_GET['historique']); ?>
<section id="timeline-usr">
	<h2>Historique de l'utilisateur <strong><?php echo $user->get_login_by_ID($usr['id']); ?></strong></h2>

	<ul class="timelineHistorique">
		<?php if ($usr['lastaction'] != '0000-00-00 00:00:00') : ?>
		<li class="connexion">
			<strong>Dernière action sur le système</strong>
			<ul>
				<li class="date"><?php echo date('d/m/Y \à H:i', strtotime($usr['lastaction'])); ?></li>
			</ul>
		</li>
		<?php endif; $histoire = $historique->rechercheParUser($usr['id']); foreach ($histoire as $key => $ligne) : ?>
		<li class="<?php echo $ligne['type']; ?>">
			<strong><?php $historique->returnType($ligne['type']); ?> &laquo;&nbsp;<?php echo $ligne['objet']; ?>&nbsp;&raquo;</strong>
			<ul>
				<li class="contact"><a class="nostyle" href="<?php $core->tpl_go_to('fiche', array('id' => $ligne['contact_id'])); ?>"><?php echo $fiche->affichageNomByID($ligne['contact_id']); ?></a></li>
				<li class="date"><?php echo date('d/m/Y \à H:i', strtotime($ligne['timestamp'])); ?></li>
			</ul>
		</li>
		<?php endforeach; ?>
		<li class="fin">
			<strong>Fin de l'historique</strong>
		</li>
	</ul>
</section>