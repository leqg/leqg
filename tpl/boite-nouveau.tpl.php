<section id="fiche">
	<header class="porte">
		<h2>Nouvelle mission de boîtage</h2>
	</header>
	
	<?php if (isset($_GET['rue'])) : ?>
	
		<form action="ajax.php?script=boite-creation" method="post">
			<input type="hidden" name="ville" value="<?php echo $_GET['ville']; ?>">
			<input type="hidden" name="rue" value="<?php echo $_GET['rue']; ?>">
			<ul class="deuxColonnes">
				<ul style="display: none;">
					<?php $immeubles = $carto->listeImmeubles($_GET['rue']); foreach ($immeubles as $immeuble) : ?>
					<input type="checkbox" name="immeubles[]" value="<?php echo $immeuble['id']; ?>" class="checkImmeuble" id="immeuble-<?php echo $immeuble['id']; ?>">
					<?php endforeach; ?>
				</ul>
				<li class="submit">
					<input type="submit" value="Créer la mission">
				</li>
				<li>
					<span class="label-information">Ville</span>
					<ul class="listeEncadree">
						<a href="<?php $core->tpl_go_to('boite', array('action' => 'nouveau')); ?>" title="Retour au choix de la ville">
							<li class="ville">
								<strong><?php $carto->afficherVille($_GET['ville']); ?></strong>
							</li>
						</a>
					</ul>
				</li>
				<li>
					<span class="label-information">Rue</span>
					<ul class="listeEncadree">
						<a href="<?php $core->tpl_go_to('boite', array('action' => 'nouveau', 'ville' => $_GET['ville'])); ?>" title="Retour au choix de la rue">
							<li class="rue">
								<strong><?php $carto->afficherRue($_GET['rue']); ?></strong>
							</li>
						</a>
					</ul>
				</li>
				<li>
					<span class="label-information">Immeubles sélectionnés</span>
					<ul class="listeEncadree">
						<?php $immeubles = $carto->listeImmeubles($_GET['rue']); foreach ($immeubles as $immeuble) : $electeurs = count($carto->listeElecteurs($immeuble['id'])); ?>
						<label for="immeuble-<?php echo $immeuble['id']; ?>">
							<li class="immeuble cursor " id="labelImmeuble-<?php echo $immeuble['id']; ?>" data-immeuble="<?php echo $immeuble['id']; ?>" data-rue="<?php echo $_GET['rue']; ?>" data-ville="<?php echo $_GET['ville']; ?>">
								<strong><?php echo $immeuble['numero']; ?> <?php echo trim($carto->afficherRue($_GET['rue'])); ?></strong>
								<p><?php $carto->bureauDeVote($immeuble['id'], false, true); ?> – <strong><?php echo $electeurs; ?> électeur<?php echo ($electeurs > 1) ? 's' : ''; ?></strong></p>
							</li>
						</label>
						<?php endforeach; ?>
					</ul>
				</li>
			</ul>
		</form>
		
	<?php elseif (isset($_GET['ville'])) : ?>
	
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Ville</span>
				<ul class="listeEncadree">
					<li class="ville">
						<strong><?php $carto->afficherVille($_GET['ville']); ?></strong>
					</li>
				</ul>
			</li>
			<li>
				<span class="label-information"><label for="recherche-rue">Rue</label></span>
				<input type="text" name="recherche-rue" id="recherche-rue" data-ville="<?php echo $_GET['ville']; ?>">
			</li>
			<li id="liste-rue">
				<ul class="listeEncadree" id="resultats-rue"></ul>
			</li>
		</ul>
		
	<?php else : ?>
	
		<ul class="deuxColonnes">
			<li>
				<span class="label-information"><label for="recherche-ville">Ville</label></span>
				<input type="text" name="recherche-ville" id="recherche-ville">
			</li>
			<li id="liste-ville">
				<ul class="listeEncadree" id="resultats-ville"></ul>
			</li>
		</ul>
		
	<?php endif; ?>
	
</section>