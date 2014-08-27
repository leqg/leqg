<section id="fiche">
	<header class="poste">
		<h2>
			Nouvelle campagne de publipostage
		</h2>
	</header>
	
	<?php if (isset($_GET['ciblage'])) : ?>
	<form action="ajax.php?script=poste-estimation" method="post" id="export">
		<?php
			$query = 'SELECT * FROM envois WHERE envoi_id = ' . $_GET['ciblage'];
			$sql = $db->query($query);
			$campagne = $core->formatage_donnees($sql->fetch_assoc());
		?>
		<input type="hidden" name="campagne" id="campagne-id" value="<?php echo $campagne['id']; ?>">
		<input type="hidden" name="texte" id="tailleTexte" value="<?php echo $campagne['texte']; ?>">
		<input type="hidden" name="canton" value="">
		<input type="hidden" name="ville" value="<?php if (isset($_GET['ville'])) echo $_GET['ville']; ?>">
		<input type="hidden" name="rue" value="<?php if (isset($_GET['rue'])) echo $_GET['rue']; ?>">
		<input type="hidden" name="immeuble" value="<?php if (isset($_GET['immeuble'])) echo $_GET['immeuble']; ?>">
		<input type="hidden" name="mobile" value="0">
		<input type="hidden" name="email" value="0">
		<input type="hidden" name="fixe" value="0">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Campagne</span>
				<p><?php echo $campagne['titre']; ?></p>
			</li>
			<li>
				<span class="label-information">Critère géographique</span>
				<p>
					<a class="nostyle bouton boutonOrange" href="<?php $core->tpl_go_to('poste', array('action' => 'nouveau', 'ciblage' => $_GET['ciblage'], 'criteresGeographiques' => 'true')); ?>">Choisir</a>
					<em id="critereGeographique" style="padding-left: 1em">
						<?php if (isset($_GET['immeuble'])) : ?>
						<?php $carto->afficherImmeuble($_GET['immeuble']); ?> <?php $carto->afficherRue($_GET['rue']); ?>, <?php $carto->afficherVille($_GET['ville']); ?>
						<?php elseif (isset($_GET['rue'])) : ?>
						<?php $carto->afficherRue($_GET['rue']); ?>, <?php $carto->afficherVille($_GET['ville']); ?>
						<?php elseif (isset($_GET['ville'])) : ?>
						<?php $carto->afficherVille($_GET['ville']); ?>
						<?php endif; ?>
					</em>
				</p>
			</li>
			<li id="electeur">
				<span class="label-information"><label>Est électeur</label></span>
				<p class="radioBool">
					<label for="electeur-oui"><input type="radio" name="electeur" id="electeur-oui" value="1">&nbsp;Nécessaire</label>
					<label for="electeur-non"><input type="radio" name="electeur" id="electeur-non" value="0" checked>&nbsp;Indifférent</label>
				</p>
			</li>
			<li id="selectionSexe">
				<span class="label-information"><label for="form-sexe">Sexe</label></span>
				<span class="bordure-form"><label class="selectbox"><select name="sexe" id="form-sexe"><option value="i">Indifférent</option><option value="m">Homme</option><option value="f">Femme</opion></select></label></span>
			</li>
			<li id="selectionAgeMin">
				<span class="label-information"><label for="form-age-min">&Acirc;ge minimal</label></span>
				<span class="bordure-form"><label class="selectbox"><select name="age-min" id="form-age-min"><option value="0">Indifférent</option><?php $age = 18; while ($age <= 100) { ?><option value="<?php echo $age; ?>"><?php echo $age; ?> ans</option><?php $age++; } ?></select></label></span>
			</li>
			<li id="selectionAgeMax">
				<span class="label-information"><label for="form-age-max">&Acirc;ge maximal</label></span>
				<span class="bordure-form"><label class="selectbox"><select name="age-max" id="form-age-max"><option value="0">Indifférent</option><?php $age = 18; while ($age <= 100) { ?><option value="<?php echo $age; ?>"><?php echo $age; ?> ans</option><?php $age++; } ?></select></label></span>
			</li>
			<li id="thematiques">
				<span class="label-information"><label for="tags">Thématique</label></span>
				<input type="text" id="tags" name="tags" placeholder="e.g. sport">
			</li>
			<li class="submit">
				<input type="submit" value="Estimer le nombre de fiches">
			</li>
		</ul>
	</form>
	<?php else : ?>
	<form action="ajax.php?script=poste-nouveau" method="post">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information"><label for="poste-objet">Titre de la campagne</label></span>
				<input type="text" name="titre" id="poste-titre">
			</li>
			<li>
				<span class="label-information"><label for="poste-contenu">Description</label></span>
				<textarea name="texte" id="poste-contenu"></textarea>
			</li>
			<li class="submit">
				<input type="submit" value="Définir le ciblage">
			</li>
		</ul>
	</form>
	<?php endif; ?>
</section>

<aside>
	<?php if (isset($_GET['ciblage'])) : ?>
		<?php if (isset($_GET['criteresGeographiques'])) : ?>
		<div id="geo">
			<nav class="navigationFiches">
				<a class="retour" href="<?php $core->tpl_go_to('poste', array('action' => 'nouveau', 'ciblage' => $_GET['ciblage'])); ?>">Annuler le critère géographique</a>
			</nav>
			
			
			<?php if (isset($_GET['rue'])) : ?>
			
			<h6>Validation du critère géographique</h6>
	
			<ul class="listeEncadree">
				<a href="<?php $core->tpl_go_to('poste', array('action' => 'nouveau', 'ciblage' => $_GET['ciblage'], 'ville' => $_GET['ville'], 'rue' => $_GET['rue'])); ?>">
					<li class="rue">
						<strong>Valider la sélection &laquo;&nbsp;<em><?php $carto->afficherRue($_GET['rue']); ?></em>&nbsp;&raquo;</strong>
						<p><?php $carto->afficherVille($_GET['ville']); ?></p>
					</li>
				</a>
			</ul>
			
			<h6>Sélection d'un critère plus précis</h6>
			
			<ul class="listeEncadree" id="immeuble-resultats">
				<?php $immeubles = $carto->listeImmeubles($_GET['rue']); foreach($immeubles as $immeuble) : ?>
				<a href="<?php $core->tpl_go_to('poste', array('action' => 'nouveau', 'ciblage' => $_GET['ciblage'], 'ville' => $_GET['ville'], 'rue' => $immeuble['rue_id'], 'immeuble' => $immeuble['id'])); ?>">
					<li class="immeuble">
						<strong><?php echo $immeuble['numero']; ?> <?php $carto->afficherRue($immeuble['rue_id']); ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
			
			<?php elseif (isset($_GET['ville'])) : ?>
			
			<h6>Validation du critère géographique</h6>
	
			<ul class="listeEncadree">
				<a href="<?php $core->tpl_go_to('poste', array('action' => 'nouveau', 'ciblage' => $_GET['ciblage'], 'ville' => $_GET['ville'])); ?>">
					<li class="ville">
						<strong>Valider la sélection &laquo;&nbsp;<em><?php $carto->afficherVille($_GET['ville']); ?></em>&nbsp;&raquo;</strong>
					</li>
				</a>
			</ul>
			
			<h6>Sélection d'un critère plus précis</h6>
			
			<ul class="deuxColonnes">
				<li>
					<span class="label-information"><label for="rue-recherche">Rue :</label></span>
					<input type="text" name="rue-recherche" id="rue-recherche" data-ville="<?php echo $_GET['ville']; ?>" data-ciblage="<?php echo $_GET['ciblage']; ?>">
				</li>
			</ul>
			<ul class="listeEncadree" id="rue-resultats"></ul>
			
			<?php else : ?>
			
			<h6>Mise en place d'un critère géographique</h6>
			
			<ul class="deuxColonnes">
				<li>
					<span class="label-information"><label for="ville-recherche">Ville :</label></span>
					<input type="text" name="ville-recherche" id="ville-recherche" data-ciblage="<?php echo $_GET['ciblage']; ?>">
				</li>
			</ul>
			<ul class="listeEncadree" id="ville-resultats"></ul>
			
			<?php endif; ?>
		</div>
		<?php else : ?>
		<div id="estimation">
			<h6>Estimation du nombre de fiches ciblées</h6>
			<p>
				D'après les critères sélectionnés, <strong id="affichageEstimation">0</strong> fiches seront contactées.
			</p>
			<p style="text-align: center;" id="boutonExportation">
				<a href="ajax.php?script=poste-export&campagne=<?php echo $campagne['id']; ?>" id="exportation" class="nostyle bouton boutonRouge">Lancer la campagne</a>
			</p>
			<div id="calcul"><p><span><span>&#xe8eb;</span></span>Calcul en cours</p></div>
		</div>
		<?php endif; ?>
	<?php endif; ?>
</aside>