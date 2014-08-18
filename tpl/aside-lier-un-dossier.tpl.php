<div id="lierUnDossier">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id(), 'interaction' => $_GET['interaction'])); ?>">Retour à l'interaction</a>
	</nav>
	<h6>Associer l'interaction à un dossier</h6>
	
	<ul class="deuxColonnes petit">
		<li>
			<span class="label-information"><i class="miroir">&#xe803;</i></span>
			<input type="text" name="dossier" id="rechercheDossier" data-fiche="<?php echo $_GET['id']; ?>" data-interaction="<?php echo $_GET['interaction']; ?>" placeholder="recherchez un dossier">
		</li>
		<li>
			<span class="label-information">Dossiers<br> ouverts</span>
			<ul id="liste-dossiers" class="listeEncadree">
				<a href="<?php $core->tpl_go_to('fiche', array('id' => $_GET['id'], 'interaction' => $_GET['interaction'], 'creerDossier' => 'true')); ?>">
					<li class="dossier ajoutDossier">
						<strong>Créer un nouveau dossier</strong>
					</li>
				</a>
			
				<?php $dossiers = $dossier->recherche(); foreach ($dossiers as $d) : ?>
				<a href="ajax.php?script=lier-rapidement-dossier&fiche=<?php echo $_GET['id']; ?>&interaction=<?php echo $_GET['interaction']; ?>&dossier=<?php echo $d['id']; ?>">
					<li class="dossier">
						<strong><?php echo $d['nom']; ?></strong>
						<p><?php echo $d['description']; ?></p>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</div>