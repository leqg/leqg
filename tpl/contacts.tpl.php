<?php 
	User::protection(5);
	Core::tpl_header();
?>

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
								<option value="1">Sans email uniquement</option>
								<option value="0" selected>Indifférent</option>
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
								<option value="1">Sans mobile uniquement</option>
								<option value="0" selected>Indifférent</option>
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
								<option value="1">Sans fixe uniquement</option>
								<option value="0" selected>Indifférent</option>
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
								<option value="1">Le contact n'est pas électeur</option>
								<option value="0" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
			</ul>
		</section>
	</div>
	
	<div class="colonne demi droite">
		<section class="contenu demi absenceCriteres">
			<h3 class="manqueCritere">Indiquez un critère pour lancer le tri</h3>
		</section>
		
        <?php $liste = Evenement::last( 5 ); if (count($liste)) : ?>
        <section class="contenu demi">
        	    <h4>Dernières interactions</h4>
        	    
        	    <ul class="listeDesEvenements">
            	    <?php foreach ($liste as $event) : $e = new Evenement(md5($event['historique_id'])); $c = new Contact(md5($e->get('contact_id'))); ?>
					<li class="evenement <?php echo $e->get_infos('type'); ?> <?php if ($e->lien()) { ?>clic<?php } ?>">
						<small><span><?php echo Core::tpl_typeEvenement($e->get_infos('type')); ?></span></small>
						<strong><a href="<?php echo Core::tpl_go_to('contact', array('contact' => md5($c->get('contact_id')), 'evenement' => md5($e->get('historique_id')))); ?>"><?php echo (!empty($e->get_infos('objet'))) ? $e->get_infos('objet') : 'Événement sans titre'; ?></a></strong>
						<ul class="infosAnnexes">
							<li class="contact"><a href="<?php echo Core::tpl_go_to('contact', array('contact' => md5($c->get('contact_id')))); ?>"><?php echo $c->noms(' '); ?></a></li>
							<li class="date"><?php echo date('d/m/Y', strtotime($e->get_infos('date'))); ?></li>
						</ul>
					</li>
	                <?php endforeach; ?>
        	    </ul>
    	    </section>
	    <?php endif; ?>
		
		<section class="contenu demi invisible actionsFiches">
			<ul class="iconesActions">
				<li class="smsSelection">SMS groupé à la sélection</li>
				<li class="emailSelection">Email groupé à la sélection</li>
				<li class="exportSelection">Export de la sélection</li>
			</ul>
		</section>
		
		<section class="contenu demi invisible listeFiches">
			<h4>Liste des fiches selon le tri</h4>
			<input type="hidden" id="nombreFiches" value="0">
			
			<ul class="listeContacts resultatTri"></ul>
		</section>
	</div>

<?php Core::tpl_footer(); ?>