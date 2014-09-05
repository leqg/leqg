	</main><!--#central-->
	
	<!-- Volet de notification -->
	<aside id="notificationCenter">
	<?php if ($notification->nombre()) : ?>
		<h6>Tâches à réaliser</h6>
		<ul class="listeIcone">
			<?php $taches = $tache->recherche($user->get_the_id()); foreach ($taches as $t) : ?>
			<li class="tache">
				<strong><?php echo $t['description']; ?></strong>
				<?php if (!is_null($t['deadline'])) { ?><em>Deadline <?php echo date('d/m/Y', $t['deadline']) ;?></em><?php } ?>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<div id="vide">Aucune notification</div>
	<?php endif; ?>
	</aside>
</body>
</html>