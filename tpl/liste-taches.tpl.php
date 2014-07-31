<section id="liste-taches" class="central">
	<h3>Tâches restantes à réaliser</h3>
	<ul class="taches">
		<?php $taches = $tache->recherche( $user->get_the_id() ); foreach ( $taches as $t ) : ?>
		<li>
			<span class="description"><?php echo $t['description']; ?></span>
			<ul class="fiches">
				<?php $contacts = explode(',', $t['contacts']); foreach ( $contacts as $contact ) : ?>
				<li><a href="<?php $core->tpl_get_url('fiche', $contact); ?>"><?php $fiche->nomByID( $contact , 'span' ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</li>
		<?php endforeach; ?>
	</ul>
</section>