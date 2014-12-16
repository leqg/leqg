	</main><!--#central-->
	
	<!-- Volet de notification -->
	<aside id="notificationCenter">
		<?php
			//$taches = Evenement::taches_personnelles();
			$taches = false;
			if ($taches) :
		?>
		<h4>Tâches à réaliser</h4>
		
		<ul class="tachesPersonnelles">
			<?php
				foreach ($taches as $tache) :
					$event = new Evenement($tache['historique_id'], false);
					$fiche = new Contact(md5($event->get('contact_id')));
			?>
			<li>
				<strong><?php echo $tache['tache_description']; ?></strong>
				<a href="<?php Core::tpl_go_to('contact', array('contact' => md5($fiche->get('contact_id')), 'evenement' => md5($event->get('historique_id')))); ?>">Accéder aux informations</a>
				<!--<a href="">Marquer la tâche comme terminée</a>-->
			</li>
			<?php endforeach; ?>
		</ul>
		<?php else : ?>
		<div id="vide">Aucune notification</div>
		<?php endif; ?>
	</aside>
</body>
</html>
