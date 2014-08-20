<?php
	
	// On va charger les 10 dernières interactions de la page d'accueil contact
	$interactions = $historique->dernieresInteractions(10);

?>
<section id="fiche">

	<h3>Derniers événements enregistrés dans le QG</h3>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Électeurs concernés</span>
			<ul class="listeEncadree">
				<?php foreach($interactions as $interaction) : ?>
				<a class="nostyle" href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'])); ?>">
					<li class="electeur">
						<strong><?php $fiche->affichageNomById($interaction['contact_id']); ?></strong>
						<p class="tailleNormale">
							<em><?php echo date('d/m/Y', strtotime($interaction['date'])); ?> &bull; <?php $historique->returnType($interaction['type']); ?></em> – <?php echo $interaction['objet']; ?>
						</p>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	
</section>