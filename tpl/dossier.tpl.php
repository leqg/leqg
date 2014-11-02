<?php
	// On vérifie l'existance d'un élément dossier
	if (!isset($_GET['dossier'])) Core::tpl_go_to('dossier', true);
	
	// On ouvre l'objet dossier
	$dossier = new Folder($_GET['dossier']);

	// On affiche l'entête
	Core::tpl_header();
?>
	
	<h2 class="titre" data-dossier="<?php echo $dossier->get('dossier_id'); ?>"><?php echo $dossier->get('dossier_nom'); ?></h2>

	<div class="colonne demi gauche">
		<section class="contenu demi description">
			<h4>Description <small class="clicable modifierDescription">[modifier]</small></h4>
			<p><?php echo $dossier->get('dossier_description'); ?></p>
		</section>
		
		<section class="contenu demi contacts">
			<h4>Événements concernés par ce dossier</h4>
			
			<ul class="listeDesEvenements listing">
				<?php $evenements = $dossier->evenements(); foreach ($evenements as $evenement) : $e = new evenement(md5($evenement['historique_id'])); $c = new contact(md5($e->get('contact_id'))); ?>
				<li class="objet <?php echo $e->get_infos('type'); ?>">
					<small><span><?php echo Core::tpl_typeEvenement($e->get_infos('type')); ?></span></small>
					<strong><a href="<?php Core::tpl_go_to('contact', array('contact' => md5($e->get('contact_id')), 'evenement' => md5($e->get('historique_id')))); ?>"><?php echo $e->get_infos('objet'); ?></a></strong>
					<ul class="infosAnnexes">
						<li class="date"><?php echo date('d/m/Y', strtotime($e->get_infos('date'))); ?></li>
						<li class="contact"><a href="<?php Core::tpl_go_to('contact', array('contact' => md5($e->get('contact_id')))); ?>"><?php echo $c->noms(' '); ?></a></li>
					</ul>
				</li>
				<?php endforeach; ?>
			</ul>
		</section>
		
		<section class="contenu demi">
			<h4>Fichiers liés à ce dossier</h4>
			
			<ul class="listeDesFichiers">
				<?php $fichiers = $dossier->fichiers(); if (count($fichiers)) : foreach ($fichiers as $fichier) : $e = new evenement(md5($fichier['interaction_id'])); $c = new contact(md5($e->get('contact_id'))); ?>
				<a href="uploads/<?php echo $fichier['fichier_url']; ?>" target="_blank">
					<li class="fichier">
						<strong><?php echo $fichier['fichier_nom']; ?></strong>
						<em><?php echo $fichier['fichier_description']; ?></em>
						<ul class="infosAnnexes">
							<li class="contact sansChanger"><?php echo $c->noms(' '); ?></li>
						</ul>
					</li>
				</a>
				<?php endforeach; else: ?>
				<li class="vide">
					<strong>Aucun fichier</strong>
				</li>
				<?php endif; ?>
			</ul>
		</section>
	</div>
	
	<div class="colonne demi droite">
		
		<section class="contenu demi notes">
			<h4>Notes</h4>
			<ul class="formulaire">
				<li>
					<span class="form-icon decalage notes"><textarea class="postit" id="modifierNotes" name="modifierNotes" style="height: 10em;"><?php echo $dossier->get('dossier_notes'); ?></textarea></span>
				</li>
			</ul>
		</section>
		
		<section class="contenu demi">
			<h4>Tâches liées à ce dossier</h4>
			
			<ul class="listeDesTaches">
				<?php $taches = $dossier->taches(); if (count($taches)) : foreach ($taches as $tache) : $e = new evenement(md5($tache['historique_id'])); $c = new contact(md5($e->get('contact_id'))); ?>
				<li class="tache">
					<strong><?php echo $tache['tache_description']; ?></strong>
					<ul class="infosAnnexes">
						<li class="contact sansChanger"><?php echo $c->noms(' '); ?></li>
					</ul>
				</li>
				<?php endforeach; else: ?>
				<li class="vide">
					<strong>Aucune tâche</strong>
				</li>
				<?php endif; ?>
			</ul>
		</section>
		
		<section class="contenu demi invisible modifDescription">
    	    <a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Modification de la description</h4>

			<ul class="formulaire">
				<li>
					<label class="small" for="modificationDescription">Description</label>
					<span class="form-icon decalage notes"><textarea id="modificationDescription" name="modificationDescription"><?php echo $dossier->get('dossier_description'); ?></textarea></span>
				</li>
				<li>
					<button class="validerModificationDescription">Changer la description</button>
				</li>
			</ul>
		</section>
		
		<section class="contenu demi invisible modifTitre">
    	    <a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Modification du titre du dossier</h4>

			<ul class="formulaire">
				<li>
					<label class="small" for="modificationTitre">Titre du dossier</label>
					<span class="form-icon decalage objet"><input type="text" id="modificationTitre" name="modificationTitre" value="<?php echo $dossier->get('dossier_nom'); ?>"></span>
				</li>
				<li>
					<button class="validerModificationTitre">Changer le titre</button>
				</li>
			</ul>
		</section>
		
	</div>
	
<?php Core::tpl_footer(); ?>