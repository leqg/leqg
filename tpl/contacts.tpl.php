<?php Core::tpl_header(); ?>

	<h2>Votre fichier contacts consolidé</h2>
	
	<form class="rechercheGlobale" action="index.php?page=recherche" method="post">
		<span class="search-icon">
			<input type="search" name="recherche" placeholder="Recherche de fiche">
			<span class="annexesRecherche">
				<span class="iconeRecherche"></span>
				<input type="submit" class="lancementRecherche" value="&#xe8af;">
			</span>
		</span>
	</form>
	
	<div class="colonne demi gauche">
		<section class="contenu demi">
			<ul class="iconesActions">
				<a href="<?php Core::tpl_go_to('contact', array('operation' => 'creation')); ?>"><li class="new">Nouvelle fiche</li></a>
				<a href="<?php Core::tpl_go_to('fiche', array('operation' => 'fusion')); ?>"><li class="merge">Fusion de fiches</li></a>
			</ul>
		</section>
		
		<section class="contenu demi">
			<h4>Critères géographiques de tri</h4>
			
			<ul class="listeTris">
				<li class="tri ajoutTri">Ajout d'un critère de tri</li>
			</ul>
		</section>
		
		<section class="contenu demi">
			<h4>Critères généraux de tri</h4>
			
			<ul class="formulaire serre">
				<li>
					<label class="small" for="coordonnees-email">Email</label>
					<span class="form-icon email">
						<label class="sbox" for="coordonnees-email">
							<select name="coordonnees-email" id="coordonnees-email" class="selectionTri">
								<option value="2">Avec email uniquement</option>
								<option value="0">Sans email uniquement</option>
								<option value="1" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
				<li>
					<label class="small" for="coordonnees-mobile">Téléphone mobile</label>
					<span class="form-icon mobile">
						<label class="sbox" for="coordonnees-mobile">
							<select name="coordonnees-mobile" id="coordonnees-mobile" class="selectionTri">
								<option value="2">Avec mobile uniquement</option>
								<option value="0">Sans mobile uniquement</option>
								<option value="1" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
				<li>
					<label class="small" for="coordonnees-fixe">Téléphone fixe</label>
					<span class="form-icon telephone">
						<label class="sbox" for="coordonnees-fixe">
							<select name="coordonnees-fixe" id="coordonnees-fixe" class="selectionTri">
								<option value="2">Avec fixe uniquement</option>
								<option value="0">Sans fixe uniquement</option>
								<option value="1" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
				<li>
					<label class="small" for="electeur">Liste électorale</label>
					<span class="form-icon utilisateur">
						<label class="sbox" for="coordonnees-electeur">
							<select name="coordonnees-electeur" id="coordonnees-electeur" class="selectionTri">
								<option value="2">Le contact est électeur</option>
								<option value="0">Le contact n'est pas électeur</option>
								<option value="1" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
			</ul>
		</section>
	</div>
	
	<div class="colonne demi droite">
		<section class="contenu demi">
			<h3 class="manqueCritere">Indiquez un critère pour lancer le tri</h3>
		</section>
		
		<section class="contenu demi">
			<h4>Dernières fiches ajoutées</h4>
			
			<ul class="listeContacts">
				<?php $fiches = Contact::last(); foreach ($fiches as $fiche) : $contact = new Contact(md5($fiche)); ?>
				<a href="<?php Core::tpl_go_to('contact', array('contact' => md5($contact->get('contact_id')))); ?>" class="nostyle">
					<li class="contact <?php if ($contact->get('contact_sexe') == 'M') { echo 'homme'; } elseif ($contact->get('contact_sexe') == 'F') { echo 'femme'; } else { echo 'isexe'; } ?>">
						<strong><?php echo $contact->noms(' '); ?></strong>
						<p><?php if ($contact->contact['contact_naissance_date'] != '0000-00-00') { echo $contact->age(); } else { echo '<span class="inconnu">Âge inconnu</span>'; } ?> - <?php echo $contact->ville(); ?></p>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</section>
	</div>

<?php Core::tpl_footer(); ?>