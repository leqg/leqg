<section id="fiche" data-fiche="<?php echo $ville['id']; ?>">
	<header class="export">
		<h2>
			Module d'export de fiches
		</h2>
	</header>
	
	<form action="ajax.php?script=estimation-export" method="post" id="export">
		<input type="hidden" name="canton" value="">
		<input type="hidden" name="ville" value="<?php if (isset($_GET['ville'])) echo $_GET['ville']; ?>">
		<input type="hidden" name="rue" value="<?php if (isset($_GET['rue'])) echo $_GET['rue']; ?>">
		<input type="hidden" name="immeuble" value="<?php if (isset($_GET['immeuble'])) echo $_GET['immeuble']; ?>">
		<ul class="deuxColonnes">
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
			<li>
				<span class="label-information">Critère géographique</span>
				<p>
					<a class="nostyle bouton boutonOrange" href="<?php $core->tpl_go_to('carto', array('module' => 'export', 'criteresGeographiques' => 'true')); ?>">Choisir</a>
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
			<li id="thematiques">
				<span class="label-information"><label for="tags">Thématique</label></span>
				<input type="text" id="tags" name="tags" placeholder="e.g. sport">
			</li>
			<li id="emailConnu">
				<span class="label-information"><label>Email connu</label></span>
				<p class="radioBool">
					<label for="email-oui"><input type="radio" name="email" id="email-oui" value="1">&nbsp;Nécessaire</label>
					<label for="email-non"><input type="radio" name="email" id="email-non" value="0" checked>&nbsp;Indifférent</label>
				</p>
			</li>
			<li id="mobileConnu">
				<span class="label-information"><label>Mobile connu</label></span>
				<p class="radioBool">
					<label for="mobile-oui"><input type="radio" name="mobile" id="mobile-oui" value="1">&nbsp;Nécessaire</label>
					<label for="mobile-non"><input type="radio" name="mobile" id="mobile-non" value="0" checked>&nbsp;Indifférent</label>
				</p>
			</li>
			<li id="fixeConnu">
				<span class="label-information"><label>Fixe connu</label></span>
				<p class="radioBool">
					<label for="fixe-oui"><input type="radio" name="fixe" id="fixe-oui" value="1">&nbsp;Nécessaire</label>
					<label for="fixe-non"><input type="radio" name="fixe" id="fixe-non" value="0" checked>&nbsp;Indifférent</label>
				</p>
			</li>
			<li class="submit">
				<input type="submit" value="Estimer le nombre de fiches">
			</li>
		</ul>
	</form>
</section>

<aside>
	<?php if (isset($_GET['criteresGeographiques'])) : ?>
	<div id="geo">
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('carto', array('module' => 'export')); ?>">Revenir à l'estimation</a>
		</nav>
		
		
		<?php if (isset($_GET['rue'])) : ?>
		
		<h6>Validation du critère géographique</h6>

		<ul class="listeEncadree">
			<a href="<?php $core->tpl_go_to('carto', array('module' => 'export', 'ville' => $_GET['ville'], 'rue' => $_GET['rue'])); ?>">
				<li class="rue">
					<strong>Valider la sélection &laquo;&nbsp;<em><?php $carto->afficherRue($_GET['rue']); ?></em>&nbsp;&raquo;</strong>
					<p><?php $carto->afficherVille($_GET['ville']); ?></p>
				</li>
			</a>
		</ul>
		
		<h6>Sélection d'un critère plus précis</h6>
		
		<ul class="listeEncadree" id="immeuble-resultats">
			<?php $immeubles = $carto->listeImmeubles($_GET['rue']); foreach($immeubles as $immeuble) : ?>
			<a href="<?php $core->tpl_go_to('carto', array('module' => 'export', 'ville' => $_GET['ville'], 'rue' => $immeuble['rue_id'], 'immeuble' => $immeuble['id'])); ?>">
				<li class="immeuble">
					<strong><?php echo $immeuble['numero']; ?> <?php $carto->afficherRue($immeuble['rue_id']); ?></strong>
				</li>
			</a>
			<?php endforeach; ?>
		</ul>
		
		<?php elseif (isset($_GET['ville'])) : ?>
		
		<h6>Validation du critère géographique</h6>

		<ul class="listeEncadree">
			<a href="<?php $core->tpl_go_to('carto', array('module' => 'export', 'ville' => $_GET['ville'])); ?>">
				<li class="ville">
					<strong>Valider la sélection &laquo;&nbsp;<em><?php $carto->afficherVille($_GET['ville']); ?></em>&nbsp;&raquo;</strong>
				</li>
			</a>
		</ul>
		
		<h6>Sélection d'un critère plus précis</h6>
		
		<ul class="deuxColonnes">
			<li>
				<span class="label-information"><label for="rue-recherche">Rue :</label></span>
				<input type="text" name="rue-recherche" id="rue-recherche" data-ville="<?php echo $_GET['ville']; ?>">
			</li>
		</ul>
		<ul class="listeEncadree" id="rue-resultats"></ul>
		
		<?php else : ?>
		
		<h6>Mise en place d'un critère géographique</h6>
		
		<ul class="deuxColonnes">
			<li>
				<span class="label-information"><label for="ville-recherche">Ville :</label></span>
				<input type="text" name="ville-recherche" id="ville-recherche">
			</li>
		</ul>
		<ul class="listeEncadree" id="ville-resultats"></ul>
		
		<?php endif; ?>
	</div>
	<?php else : ?>
	<div id="estimation">
		<h6>Estimation du nombre de fiches ciblées</h6>
		<p>
			D'après les critères sélectionnés, <strong id="affichageEstimation">0</strong> fiches seront exportées.
		</p>
		<p style="text-align: center;" id="boutonExportation">
			<a href="ajax.php?script=export" id="exportation" class="nostyle bouton boutonRouge">Exporter les fiches</a>
		</p>
		<p id="affichage-envoi"><strong class="gras">Le lien vers le fichier vous sera envoyé dans la demi-heure, une fois que celui-ci sera prêt.</strong></p>
		<div id="calcul"><p><span><span>&#xe8eb;</span></span>Calcul en cours</p></div>
	</div>
	<?php endif; ?>
</aside>